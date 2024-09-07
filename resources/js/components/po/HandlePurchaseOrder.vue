<template>
    <div>
        <create-purchase-order></create-purchase-order>
        <edit-purchase-order></edit-purchase-order>
        <view-purchase-order></view-purchase-order>
    </div>
</template>

<script>
import CreatePurchaseOrder from "./CreatePurchaseOrder.vue";
import EditPurchaseOrder from "./EditPurchaseOrder.vue";
import ViewPurchaseOrder from "./ViewPurchaseOrder.vue";

export default {
    components: { CreatePurchaseOrder, EditPurchaseOrder, ViewPurchaseOrder },
    mounted() {
        console.log("Component mounted.");
    },
};
</script>
