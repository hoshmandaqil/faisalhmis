<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\HasManySync;
use App\Models\Expense\ExpenseCategory;
use App\Models\Expense\ExpenseSlip;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory, HasManySync;
    protected $table = 'purchase_order';

    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        return $this->items()->sum(DB::raw('amount * quantity'));
    }

    /**
     * Delete items and files when po is deleted.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($slip) {
            $slip->items()->delete();
            $slip->files()->delete();
        });
    }

    /**
     * category: Relation to categories
     *
     * @return void
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category', 'id');
    }

    /**
     * items
     *
     * @return $this->hasMany()
     */
    public function items()
    {
        return $this->hasManySync(PurchaseOrderItem::class, 'po_id', 'id');
    }

    /**
     * files
     *
     * @return $this->hasMany()
     */
    public function files()
    {
        return $this->hasManySync(PurchaseOrderFile::class, 'po_id', 'id');
    }

    /**
     * insertedBy
     *
     * @return $this->belongsTo()
     */
    public function insertedByUser()
    {
        return $this->belongsTo(User::class, 'inserted_by', 'id');
    }

    /**
     * checkedBy
     *
     * @return $this->belongsTo()
     */
    public function checkedByUser()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id');
    }

    /**
     * verifiedBy
     *
     * @return $this->belongsTo()
     */
    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    /**
     * approvedBy
     *
     * @return $this->belongsTo()
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * rejectedBy
     *
     * @return $this->belongsTo()
     */
    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    /**
     * status
     *
     * @return String
     */
    public function status()
    {
        $status = 'Issued';

        if ($this->rejected_date != null && $this->rejected_by != null) {
            $status = 'Rejected';
        } else if ($this->approved_date != null && $this->approved_by != null) {
            $status = 'Approved';
        } else if ($this->verified_date != null && $this->verified_by != null) {
            $status = 'Verified';
        } else if ($this->checked_date != null && $this->checked_by != null) {
            $status = 'Checked';
        }

        return $status;
    }

    /**
     * statusChange
     *
     * @param  mixed $status
     * @param  mixed $reject_comment
     * @return boolean
     */
    public function statusChange($status, $reject_comment = null, $current_date)
    {
        // Variables
        $user = auth()->id();

        // Get PO Settings
        if (config('university.app.id') != null) {
            $setting = PurchaseOrderSetting::university()->first();
        } else {
            $setting = PurchaseOrderSetting::whereNull('application_id')->first();
        }

        // Check if user can check
        if ($status == 'check' && $setting->check == $user) {
            $this->update([
                'checked_by' => $user,
                'checked_date' => $current_date
            ]);

            return true;
        }

        // Check if user can verify
        if ($status == 'verify' && $setting->verify == $user) {
            $this->update([
                'verified_by' => $user,
                'verified_date' => $current_date
            ]);

            return true;
        }

        // Check if user can approve
        if ($status == 'approve' && $setting->approve == $user) {
            $this->update([
                'approved_by' => $user,
                'approved_date' => $current_date,
                'rejected_by' => null,
                'rejected_date' => null
            ]);

            return true;
        }

        // Check if user can reject
        if ($status == 'reject' && $setting->approve == $user) {
            $this->update([
                'rejected_by' => $user,
                'rejected_date' => $current_date,
                'reject_comment' => $reject_comment
            ]);

            return true;
        }

        // Otherwise: return false
        return false;
    }

    /**
     * Retrieves the expenses associated with this object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(ExpenseSlip::class, 'po_id', 'id')->withoutGlobalScopes();
    }

    /**
     * ScopeFilter: Filter Data
     *
     * @param  mixed $query
     * @param  mixed $filters
     * @return void
     */
    public function scopeFilter($query, array $filters)
    {
        // Filter by search query
        if ($filters['search'] ?? false) {
            $query->where('description', 'LIKE', '%' . request('search') . '%')
                ->orWhere('id', request('search'))
                ->orWhere('remarks', 'LIKE', '%' . request('search') . '%')
                ->orWhere('date', 'LIKE', '%' . request('search') . '%');
        }

        // Filter by status
        if ($filters['status'] ?? false) {
            if (request('status') == 'checked') {
                $query->whereNotNull('checked_by')
                    ->whereNotNull('checked_date')
                    ->whereNull('rejected_by')
                    ->whereNull('rejected_date')
                    ->whereNull('approved_by')
                    ->whereNull('approved_date')
                    ->whereNull('verified_by')
                    ->whereNull('verified_date');
            } elseif (request('status') == 'verified') {
                $query->whereNull('rejected_by')
                    ->whereNull('rejected_date')
                    ->whereNull('approved_by')
                    ->whereNull('approved_date')
                    ->whereNotNull('verified_by')
                    ->whereNotNull('verified_date');
            } elseif (request('status') == 'approved') {
                $query->whereNotNull('approved_by')
                    ->whereNotNull('approved_date');
            } elseif (request('status') == 'rejected') {
                $query->whereNotNull('rejected_by')
                    ->whereNotNull('rejected_date');
            } elseif (request('status') == 'approved_no_expenses') {
                $query->whereNotNull('approved_by')
                    ->whereNotNull('approved_date')
                    ->whereDoesntHave('expenses');
            } elseif (request('status') == 'un_approved') {
                $query->whereNull('approved_by')
                    ->whereNull('rejected_by')
                    ->whereNull('rejected_date')
                    ->whereNull('approved_date');
            } else {
                $query->whereNull('checked_by')
                    ->whereNull('checked_date')
                    ->whereNull('rejected_by')
                    ->whereNull('rejected_date')
                    ->whereNull('approved_by')
                    ->whereNull('approved_date')
                    ->whereNull('verified_by')
                    ->whereNull('verified_date');
            }
        }

        // Filter by application
        if ($filters['application_id'] ?? false) {
            $query->where('application_id', request('application_id'));
        }
    }
}
