<template>
    <button type="button" :class="css" @click="live_stream">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "AxiosButton",
    props:["css","route","elements","message","required","target","record_id","operation_id","tree_select"],
    methods:{
        required_field() {
            let self = this;
            if (self.$props.required) {
                let values = [];
                for(let i = 0; i < self.$props.required.length; i++) {
                    let item = self.$props.record_id ? $(`${self.$props.required[i]}_${self.$props.record_id}`) : $(`${self.$props.required[i]}`);
                    if (item.val() === "" || !item.val().replace(/\s/g, '').length) {
                        if ($(item).hasClass("selectpicker-select")) {
                            !$(item).closest(".bootstrap-select").find(".dropdown-toggle").hasClass("is-invalid") ? $(item).closest(".bootstrap-select").find(".dropdown-toggle").addClass("is-invalid") : null;
                            !$(item).hasClass("is-invalid") ? $(item).toggleClass("is-invalid") : null;
                            $('.selectpicker').selectpicker('refresh');
                        }
                        else if ($(item).is("input[type='file']"))
                            !$(item).closest("div").find(".file_selector_box").hasClass("is-invalid") ? $(item).closest("div").find(".file_selector_box").addClass("is-invalid") : null;
                        else
                            !$(item).hasClass("is-invalid") ? $(item).addClass("is-invalid") : null;
                    }
                    else {
                        $(item).removeClass("is-invalid");
                        values.push(item.val());
                    }
                }
                return values.length === self.$props.required.length;
            }
            else return true;
        },
        live_stream() {
            let self = this;
            if(typeof this.elements !== "undefined" && this.elements && this.required_field() || this.target && typeof this.elements === "undefined") {
                bootbox.confirm({
                    message: this.$props.message,
                    closeButton: false,
                    buttons: {
                        confirm: {
                            label: 'بله',
                            className: 'btn-success',
                        },
                        cancel: {
                            label: 'خیر',
                            className: 'btn-danger',
                        }
                    },
                    callback: function (result) {
                        if (result === true) {
                            bootbox.hideAll();
                            let data = new FormData();
                            if (self.$props.elements) {
                                self.$props.elements.forEach(function (item) {
                                    if (self.$props.record_id) {
                                        if ($(`${item}_${self.$props.record_id}`).is("input[type='file']"))
                                            data.append(item.replaceAll("#", '').replaceAll(".", ''), $(`${item}_${self.$props.record_id}`)[0].files[0]);
                                        else
                                            data.append(item.replaceAll("#", '').replaceAll(".", ''), $(`${item}_${self.$props.record_id}`).val());
                                    } else if (self.$props.operation_id) {
                                        if ($(item).is("input[type='file']"))
                                            data.append(item.replaceAll("#", '').replaceAll(".", '').replaceAll(`_${self.$props.operation_id}`, ""), $(item)[0].files[0]);
                                        else if ($(item).is("input[type='checkbox']"))
                                            data.append(item.replaceAll("#", '').replaceAll(".", '').replaceAll(`_${self.$props.operation_id}`, ""), $(item).prop("checked") === true ? $(item).val() : null);
                                        else
                                            data.append(item.replaceAll("#", '').replaceAll(".", '').replaceAll(`_${self.$props.operation_id}`, ""), $(item).val());
                                    } else {
                                        if ($(item).is("input[type='file']"))
                                            data.append(item.replaceAll("#", '').replaceAll(".", ''), $(item)[0].files[0]);
                                        else if ($(item).is("input[type='checkbox']"))
                                            data.append(item.replaceAll("#", '').replaceAll(".", '').replaceAll(`_${self.$props.operation_id}`, ""), $(item).prop("checked") === true ? $(item).val() : null);
                                        else
                                            data.append(item.replaceAll("#", '').replaceAll(".", ''), $(item).val());
                                    }
                                });
                            } else {
                                data.append("data", JSON.stringify(self.$root.$data[`${self.$props.target}`]));
                            }
                            if (self.$props.record_id)
                                data.append('id', self.$props.record_id);
                            if (self.$props.operation_id)
                                data.append('operation_id', self.$props.operation_id);
                            self.$root.$data.show_loading = true;
                            axios.post(self.$props.route, data)
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
                                    if (response.data !== null) {
                                        console.log(response.data.data)
                                        if (response.data.data)
                                            self.$root.$data[`${self.$props.target}`] = response.data.data;
                                        if (typeof response.data["import_errors"] !== "undefined" && response.data["import_errors"].length > 0) {
                                            self.$root.$data.import_errors = response.data["import_errors"];
                                        } else
                                            self.$root.$data.import_errors = [];
                                        switch (response.data["result"]) {
                                            case "success": {
                                                self.$root.refresh_selects();
                                                alertify.notify(response.data["message"], 'success', "5");
                                                break;
                                            }
                                            case "warning": {
                                                alertify.notify(response.data["message"], 'warning', "20");
                                                break;
                                            }
                                            case "fail":{
                                                alertify.notify(response.data["message"], 'error', "30");
                                                break;
                                            }
                                        }
                                    }
                                }).catch(function (error) {
                                self.$root.$data.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
                                // console.error("Error response:");
                                // console.error(error.response.data);    // ***
                                // console.error(error.response.status);  // ***
                                // console.error(error.response.headers); // ***
                            });
                        }
                    }
                });
            }
        }
    }
}
</script>
