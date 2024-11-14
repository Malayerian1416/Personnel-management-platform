<template>
    <div class="modal fade" :id="modal_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title iranyekan">{{ title }}</h5>
                </div>
                <div class="modal-body">
                    <div class="pr-4 pl-4 rtl help-body">
                        <slot></slot>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "HelpModal",
    props: ["title","modal_id"]
}
</script>
