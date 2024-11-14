<template>
    <div>
        <treeselect class="iransans" v-model="value" :name="name" :placeholder="placeholder" :multiple="is_multiple" :options="database"
                    :disabled="disabled"
                    :searchable="true"
                    :open-on-click="true"
                    :flatten-search-results="true"
                    :disable-branch-nodes="branch_node"
                    :value-consists-of="'LEAF_PRIORITY'"
                    :clearable="true"
                    v-on:select="item_selected"
                    v-on:deselect="item_deselected"/>
    </div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
    components: { Treeselect },
    props: ["database","is_multiple","placeholder","name","selected","validation_error","disabled","valueConsistsOf","branch_node"],
    data() {
        return {
            value: null,
            item: '',
        }
    },
    mounted() {
        if(this.selected)
            this.value = this.selected;
        if (this.validation_error)
            $(".vue-treeselect__control").css("border","1px solid #dc3545")
    },
    methods:{
        item_deselected(e){
            this.$emit("contract_deselected",e.id);
        },
        item_selected(e){
            this.$emit("contract_selected",e.id);
            this.$emit("multi_contract_selected",e.id);
        }
    }
}
</script>
