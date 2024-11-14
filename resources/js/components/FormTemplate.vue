<template>
    <div>
        <form id="main_submit_form" class="p-3" :action="GetRoute" method="post" enctype="multipart/form-data" v-on:submit="SubmitForm">
            <input v-if="method === 'put'" type="hidden" name="_method" value="put">
            <input type="hidden" name="_token" :value="Csrf">
            <div class="template-toolbar bg-dark pe-4 ps-4 pt-2 pb-2 position-sticky rounded-3 d-flex flex-row align-items-center justify-content-center gap-2" :class="method === 'post' ? 'top-0' : 'edit-top'">
                <button type="button" class="btn btn-outline-light toolbar-button" v-on:click="AddParagraph">
                    <i class="far fa-text fa-1-6x"></i>
                </button>
                <div class="dropdown-center">
                    <button class="btn btn-outline-light dropdown-toggle toolbar-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="far fa-page fa-1-6x"></i>
                    </button>
                    <ul class="dropdown-menu iransans">
                        <li><a class="dropdown-item active a4-size" role="button" data-size="A4" v-on:click="ChangePageSize"> صفحه A4</a></li>
                        <li><a class="dropdown-item a5-size" role="button" data-size="A5" v-on:click="ChangePageSize">صفحه A5</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-outline-light toolbar-button" data-orientation="portrait" v-on:click="ChangePageOrientation">
                    <i class="far fa-rotate fa-1-6x"></i>
                </button>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-outline-light toolbar-button" data-zoom="out" v-on:click="Zoom($event,'out')">
                        <i class="far fa-magnifying-glass-minus fa-1-6x"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light toolbar-button" data-zoom="out" v-on:click="Zoom($event,'none')">
                        <i class="far fa-magnifying-glass fa-1-6x"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light toolbar-button" data-zoom="in" v-on:click="Zoom($event,'in')">
                        <i class="far fa-magnifying-glass-plus fa-1-6x"></i>
                    </button>
                </div>
                <div class="dropdown-center">
                    <button :disabled="selected === null" class="btn btn-outline-light dropdown-toggle toolbar-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="far fa-book-font fa-1-6x"></i>
                    </button>
                    <ul class="dropdown-menu iransans">
                        <li><a class="dropdown-item active" aria-current="true" role="button" data-font_family="iransans" v-on:click="FontFamily">فونت ایران سنس</a></li>
                        <li><a class="dropdown-item" role="button" data-font_family="mitra" v-on:click="FontFamily">فونت میترا</a></li>
                        <li><a class="dropdown-item" role="button" data-font_family="nazanin" v-on:click="FontFamily">فونت نازنین</a></li>
                        <li><a class="dropdown-item" role="button" data-font_family="iranyekan" v-on:click="FontFamily">فونت ایران یکان</a></li>
                        <li><a class="dropdown-item" role="button" data-font_family="nastaliq" v-on:click="FontFamily">فونت نستعلیق</a></li>
                        <li><a class="dropdown-item" role="button" data-font_family="titr" v-on:click="FontFamily">فونت تیتر</a></li>
                    </ul>
                </div>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-font_size="smaller" v-on:click="FontSize">
                        <i class="far fa-font"></i>
                    </button>
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-font_size="bigger" v-on:click="FontSize">
                        <i class="far fa-font fa-1-6x"></i>
                    </button>
                </div>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-alignment="right" v-on:click="TextAlignment">
                        <i class="far fa-align-right fa-1-6x"></i>
                    </button>
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-alignment="center" v-on:click="TextAlignment">
                        <i class="far fa-align-center fa-1-6x"></i>
                    </button>
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-alignment="left" v-on:click="TextAlignment">
                        <i class="far fa-align-left fa-1-6x"></i>
                    </button>
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-alignment="justify" v-on:click="TextAlignment">
                        <i class="far fa-align-justify fa-1-6x"></i>
                    </button>
                </div>
                <div class="dropdown-center">
                    <button :disabled="selected === null" class="btn btn-outline-light dropdown-toggle toolbar-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="far fa-line-height fa-1-6x"></i>
                    </button>
                    <ul class="dropdown-menu iransans">
                        <li><a class="dropdown-item" role="button" data-operation="decrease" v-on:click="LineHeight">کاهش فاصله بین خطوط</a></li>
                        <li><a class="dropdown-item" role="button" data-operation="increase" v-on:click="LineHeight">افزایش فاصله بین خطوط</a></li>
                    </ul>
                </div>
                <div class="btn-group" role="group" aria-label="Basic example" v-cloak>
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-alignment="h_center" v-on:click="Alignment">
                        <i class="far fa-objects-align-center-horizontal fa-1-6x"></i>
                    </button>
                    <button type="button" :disabled="selected === null" class="btn btn-outline-light toolbar-button" data-alignment="v_center" v-on:click="Alignment">
                        <i class="far fa-objects-align-center-vertical fa-1-6x"></i>
                    </button>
                </div>
                <button :disabled="selected === null" type="button" class="btn btn-outline-light toolbar-button" v-on:click="DeleteItem">
                    <i class="far fa-trash-can fa-1-6x"></i>
                </button>
                <button class="btn btn-outline-light toolbar-button" type="button" v-on:click="OpenFileSelect">
                    <i class="far fa-image fa-1-6x"></i>
                    <input type="file" hidden id="background_file" name="background" v-on:change="SetBackground" accept=".jpg,.jpeg,.png">
                </button>
                <button class="btn btn-outline-light toolbar-button" type="button" v-on:click="RemoveBackground">
                    <i class="far fa-image-slash fa-1-6x"></i>
                </button>
                <div class="col-12 col-lg-3 col-xl-2 ms-2">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-database fa-1-6x"></i>
                        </span>
                        <select  id="dynamics" class="form-control iransans" title="کلمات پویا" data-size="30" data-live-search="true" v-on:change="InsertKeyword">
                            <option v-for="(column , index) in dynamics" :key="index" :value="column.tag">{{ column.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="master_page" class="template-page">
                <editable-paragraph v-for="paragraph in paragraphs" :key="paragraph.key" :id="paragraph.id" :content="paragraph.text" @ElementSelected="ElementSelected" :css_class="paragraph.fontFamily" :css_style='`width:${paragraph.width};height:${paragraph.height};top:${paragraph.top}px;left:${paragraph.left}px;fontSize:${paragraph.fontSize};textAlignment:${paragraph.textAlignment};lineHeight:${paragraph.lineHeight}`'>
                </editable-paragraph>
            </div>
            <div class="template-toolbar bg-dark p-4 position-sticky d-flex flex-column align-items-center justify-content-center gap-2 col-12 col-lg-3 col-xl-2 bottom-0 pb-3" style="left: 0">
                <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-file-signature fa-1-6x"></i>
                        </span>
                    <input class="form-control iransans text-center" id="name" placeholder="نام فرم" v-model="page.name">
                </div>
                <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="fas fa-memo fa-1-6x"></i>
                        </span>
                    <select  id="applications" class="form-control iransans" title="نوع فرم" data-size="30" v-model="page.application">
                        <option v-for="application in applications" :key="application.id" :value="application.application_form_type">{{ application.name }}</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import route from "ziggy-js";

export default {
    name: "FormTemplate",
    props: ["method"],
    data(){
        return {
            id: typeof form_id !== "undefined" ? form_id : null,
            page: typeof page_data !== "undefined" ? page_data : {"name":null,"application":null,"iso_type":"A4","orientation":"portrait","background":false,"contents":[]},
            background: typeof background_data !== "undefined" ? background_data : "",
            paragraphs: typeof page_data !== "undefined" ? page_data.contents : [],
            selected: null,
            regexp: /\/\/[\u0600-\u06FF\s|\u0600-\u06FF\_]*\/\//g,
            applications: typeof applications_data !== "undefined" ? applications_data : [],
            dynamics: [
                {"name":"نام","tag":"//نام//","data":"first_name"},
                {"name":"نام خانوادگی","tag":"//نام_خانوادگی//","data":"last_name"},
                {"name":"نام و نام خانوادگی","tag":"//نام_و_نام_خانوادگی//","data":"name"},
                {"name":"شماره شناسنامه","tag":"//شماره_شناسنامه//","data":"id_number"},
                {"name":"جنسیت","tag":"//جنسیت//","data":"gender"},
                {"name":"خطاب جنسیتی" , "tag":"//خطاب_جنسیتی//" , "data":"gender_refer" , "resource":"Employee"},
                {"name":"کد ملی","tag":"//کد_ملی//","data":"national_code"},
                {"name":"نام پدر","tag":"//نام_پدر//","data":"father_name"},
                {"name":"تاریخ تولد","tag":"//تاریخ_تولد//","data":"birth_date"},
                {"name":"محل تولد","tag":"//محل_تولد//","data":"birth_city"},
                {"name":"محل صدور","tag":"//محل_صدور//","data":"issue_city"},
                {"name":"تحصیلات","tag":"//تحصیلات//","data":"education"},
                {"name":"وضعیت تاهل","tag":"//وضعیت_تاهل//","data":"marital_status"},
                {"name":"تعداد فرزندان","tag":"//تعداد_فرزندان//","data":"children_count"},
                {"name":"تعداد فرزندان تحت تکلف","tag":"//تعداد_فرزندان_تحت_تکلف//","data":"included_children_count"},
                {"name":"شماره بیمه","tag":"//شماره_بیمه//","data":"insurance_number"},
                {"name":"سابقه بیمه","tag":"//سابقه_بیمه//","data":"insurance_days"},
                {"name":"وضعیت خدمت سربازی","tag":"//وضعیت_خدمت_سربازی//","data":"military_status"},
                {"name":"محل استقرار","tag":"//محل_استقرار//","data":"job_seating"},
                {"name":"عنوان شغلی","tag":"//عنوان_شغلی//","data":"job_title"},
                {"name":"نام بانک","tag":"//نام_بانک//","data":"bank_name"},
                {"name":"شماره حساب","tag":"//شماره_حساب//","data":"bank_account"},
                {"name":"شماره کارت","tag":"//شماره_کارت//","data":"credit_card"},
                {"name":"شماره شبا","tag":"//شماره_شبا//","data":"sheba_number"},
                {"name":"تلفن","tag":"//تلفن//","data":"phone"},
                {"name":"موبایل","tag":"//موبایل//","data":"mobile"},
                {"name":"آدرس","tag":"//آدرس//","data":"address"},
                {"name":"تاریخ شروع اولین قرارداد","tag":"//تاریخ_شروع_اولین_قرارداد//","data":"initial_start"},
                {"name":"تاریخ پایان اولین قرارداد","tag":"//تاریخ_پایان_اولین_قرارداد//","data":"initial_end"},
                {"name":"تاریخ شروع قرارداد جاری","tag":"//تاریخ_شروع_قرارداد_جاری//","data":"current_start"},
                {"name":"تاریخ پایان قرارداد جاری","tag":"//تاریخ_پایان_قرارداد_جاری//","data":"current_end"},
                {"name":"آخرین جمع ناخالص پرداختی","tag":"//آخرین_جمع_ناخالص_پرداختی//","data":"salary"},
                {"name":"آخرین جمع خالص پرداختی","tag":"//آخرین_جمع_خالص_پرداختی//","data":"net_salary"},
                {"name":"آخرین فیش حقوقی","tag":"//آخرین_فیش_حقوقی//","data":"last_bill"},
                {"name":"تاریخ روز","tag":"//تاریخ_روز//","data":"date"},
                {"name":"شماره نامه","tag":"//شماره_نامه//","data":"number"},
                {"name":"نشان QR شماره نامه","tag":"//نشان_QR_شماره_نامه//","data":"qrcode"},
                {"name":"گیرنده نامه","tag":"//گیرنده_نامه//","data":"recipient"},
                {"name":"موضوع نامه","tag":"//موضوع_نامه//","data":"application"},
                {"name":"وام گیرنده","tag":"//وام_گیرنده//","data":"borrower"},
            ]
        }
    },
    computed:{
        Csrf(){
            const token = document.querySelector('meta[name="csrf-token"]');
            return token.content;
        },
        GetRoute() {
            const self = this;
            if (this.method === "post")
                return route("FormTemplates.store");
            else
                return route("FormTemplates.update",[self.id]);
        },
    },
    mounted() {
        $(document).ready(() => {
            $('#dynamics,#applications').selectpicker();
        });
        if(this.background)
            document.getElementById("master_page").style.backgroundImage = "url(data:image/png;base64," + this.background + ")";
    },
    methods: {
        AddParagraph: function () {
            let GetId = new Promise(Resolve => {
                const self = this;
                let random = Math.floor(random = Math.random() * 10000);
                while (self.paragraphs.some(paragraph => paragraph.key === random))
                    random = Math.floor(Math.random() * 10000);
                Resolve(random);
            });
            GetId.then(id => {
                this.paragraphs.push({
                    "key": id,
                    "id": `p-${id}`,
                    "text": null,
                    "width": "100px",
                    "height": "25px",
                    "top": "20px",
                    "left": "85%",
                    "fontFamily": "iransans",
                    "fontSize": "12px",
                    "textAlignment": "right",
                    "alignment": null,
                    "lineHeight": "25px"
                });
            }).catch(error => {
                bootbox.alert(error);
            })
        },
        InsertKeyword(e) {
            if (this.selected) {
                document.getElementById(this.selected).innerText += e.target.value;
            }
        },
        ElementSelected(id) {
            this.selected = id;
            $("#master_page").children().each(function () {
                $(this).children().removeClass("selected");
            });
            $(`#${id}`).addClass("selected");
            this.$forceUpdate();
        },
        Zoom(e, type) {
            const element = document.getElementById("master_page");
            switch (type) {
                case "out": {
                    element.style.transform = "scale(0.75)";
                    break;
                }
                case "in": {
                    element.style.transform = "scale(1.25)";
                    break;
                }
                case "none": {
                    element.style.transform = "none";
                    break;
                }
            }
        },
        TextAlignment(e) {
            document.getElementById(this.selected).style.textAlign = `${e.currentTarget.dataset.alignment}`;
            let selected = this.paragraphs.find(paragraph => {
                return paragraph.id === this.selected;
            });
            selected.alignment = e.currentTarget.dataset.alignment;
            this.$forceUpdate();
        },
        Alignment(e) {
            document.getElementById(this.selected).style.ma = `${e.currentTarget.dataset.alignment}`;
            let selected = this.paragraphs.find(paragraph => {
                return paragraph.id === this.selected;
            });
            selected.alignment = e.currentTarget.dataset.alignment;
            this.$forceUpdate();
        },
        LineHeight(e) {
            const element = document.getElementById(this.selected);
            const operation = e.currentTarget.dataset.operation;
            let lineHeight = parseInt(window.getComputedStyle(document.querySelector(`#${this.selected}`)).lineHeight.replace("px", ""));
            operation === "decrease" ? element.style.lineHeight = `${lineHeight - 3}px` : element.style.lineHeight = `${lineHeight + 3}px`;
            let selected = this.paragraphs.find(paragraph => {
                return paragraph.id === this.selected;
            });
            selected.lineHeight = operation === "decrease" ? `${lineHeight - 3}px` : `${lineHeight + 3}px`;
            this.$forceUpdate();
        },
        FontSize(e) {
            const size = e.currentTarget.dataset.font_size;
            const element = document.getElementById(this.selected);
            const currentSize = parseInt(window.getComputedStyle(document.querySelector(`#${this.selected}`)).fontSize.replace("px", ""));
            switch (size) {
                case "smaller":
                    element.style.fontSize = `${currentSize - 2}px`;
                    break;
                case "bigger":
                    element.style.fontSize = `${currentSize + 2}px`;
                    break;
            }
            let selected = this.paragraphs.find(paragraph => {
                return paragraph.id === this.selected;
            });
            selected.fontSize = size === "smaller" ? `${currentSize - 2}px` : `${currentSize + 2}px`;
            this.$forceUpdate();
        },
        FontFamily(e) {
            const font = e.currentTarget.dataset.font_family;
            const element = document.getElementById(this.selected);
            $(element).removeClass("iransans").removeClass("mitra").removeClass("nazanin").removeClass("titr").removeClass("nastaliq").addClass(font);
            let selected = this.paragraphs.find(paragraph => {
                return paragraph.id === this.selected;
            });
            selected.fontFamily = e.currentTarget.dataset.font_family;
            this.$forceUpdate();
        },
        DeleteItem() {
            document.getElementById(this.selected).remove();
            this.selected = null;
        },
        OpenFileSelect() {
            document.querySelector("#background_file").click();
        },
        SetBackground(e) {
            const self = this;
            let valid_ext = ["jpg", "jpeg", "png"];
            let file_ext = e.target.files[0].name.split('.').pop();
            let file_size = parseInt(e.target.files[0].size);
            if (valid_ext.indexOf(file_ext.toLowerCase()) === -1) {
                bootbox.alert({
                    "message": "فرمت فایل مورد قبول نمی باشد",
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.filename = 'انتخاب کنید';
            } else if (file_size > 1000000) {
                bootbox.alert({
                    "message": "حجم فایل انتخاب شده بیشتر از 800 کیلوبایت می باشد",
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.filename = 'فایلی انتخاب نشده است';
            } else {
                let reader = new FileReader();
                reader.onloadend = function () {
                    document.getElementById("master_page").style.backgroundImage = "url(" + reader.result + ")";
                    self.page.background = true;
                    self.$forceUpdate();
                }
                if (e.target.files[0])
                    reader.readAsDataURL(e.target.files[0]);
            }
        },
        RemoveBackground() {
            document.getElementById("master_page").style.backgroundImage = "repeating-linear-gradient(0deg, rgb(221, 221, 221) 0px, rgb(221, 221, 221) 1px,transparent 1px, transparent 21px),repeating-linear-gradient(90deg, rgb(221, 221, 221) 0px, rgb(221, 221, 221) 1px,transparent 1px, transparent 21px),linear-gradient(90deg, hsl(104,0%,96%),hsl(104,0%,96%))";
            this.page.background = false;
            this.$forceUpdate();
        },
        ChangePageSize(e) {
            const size = e.currentTarget.dataset.size;
            const page = $(".template-page");
            if (size === "A4") {
                $(page).css({"width": `210mm`, "height": `297mm`});
                $(".a4-size").removeClass("active").addClass("active");
                $(".a5-size").removeClass("active");
                this.page.iso_type = "A4";
                this.$forceUpdate();
            } else if (size === "A5") {
                $(page).css({"width": `148mm`, "height": `210mm`});
                $(".a5-size").removeClass("active").addClass("active");
                $(".a4-size").removeClass("active");
                this.page.iso_type = "A5";
                this.$forceUpdate();
            }
        },
        ChangePageOrientation() {
            const page = $(".template-page");
            $(page).css({"width": `${$(page).innerHeight()}px`, "height": `${$(page).innerWidth()}px`});
            $(page).innerHeight() > $(page).innerWidth() ? this.page.orientation = "portrait" : this.page.orientation = "landscape";
            this.$forceUpdate();
        },
        SubmitForm(e) {
            const self = this;
            e.preventDefault();
            const name = $("#name");
            const application = $("#applications");
            let error_flag = this.paragraphs.length === 0;
            this.method !== "post" ? this.page.contents = [] : null;
            if (this.page.name !== null && this.page.name !== "" && this.page.application !== null) {
                if (this.paragraphs.length > 0) {
                    this.paragraphs.forEach((paragraph) => {
                        const element = document.getElementById(paragraph.id);
                        if (paragraph.text) {
                            const keywords = paragraph.text.match(this.regexp);
                            if (keywords) {
                                keywords.forEach(keyword => {
                                    const exist = self.dynamics.some(dynamic => dynamic.tag === keyword.replaceAll(" ", "_"));
                                    if (!exist) {
                                        error_flag = true;
                                        element.style.background = "rgba(255,0,0,0.25)";
                                    }
                                });
                            }
                        }
                    });
                }
                else
                    bootbox.alert("حداقل یک پاراگراف برای ذخیره سازی الزامی می باشد")
                if (!error_flag) {
                    self.page.contents = self.paragraphs;
                    let input = document.createElement("INPUT");
                    input.setAttribute("type", "text");
                    input.setAttribute("hidden", "true");
                    input.setAttribute("name", "page_contents");
                    input.setAttribute("value", JSON.stringify(self.page));
                    $(input).appendTo(`#${e.target.id}`);
                    bootbox.confirm({
                        message: "آیا برای ایجاد تغییرات و ذخیره سازی اطمینان دارید؟",
                        buttons: {
                            confirm: {
                                label: 'بله',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: 'خیر',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result === true) {
                                self.$root.$data.button_loading = true;
                                self.$root.$data.show_loading = true;
                                e.target.submit();
                            }
                        }
                    });
                }
            }
            else {
                if(this.page.name === "" || this.page.name === null) {
                    name.removeClass("is-invalid").addClass("is-invalid");
                    bootbox.alert("لطفا نام فرم و نوع فرم را وارد نمایید");
                }
                if(this.page.application === "" || this.page.application === null) {
                    application.removeClass("is-invalid").addClass("is-invalid");
                    application.closest(".bootstrap-select").find(".dropdown-toggle").toggleClass("is-invalid");
                    application.selectpicker('destroy').selectpicker('refresh');
                }
            }
        }
    }
}
</script>

<style scoped>
.template-toolbar{
    box-shadow: 0 10px 10px -1px #a0a1a2;
    z-index: 49;
    flex-wrap: wrap;
}
.edit-top{
    top: 50px;
}
.template-page{
    position: relative;
    padding: 30px 20px;
    margin: 35px auto;
    box-shadow: 0 0 25px -1px #a0a1a2;
    font-size: 14px;
    width: 210mm;
    height: 297mm;
    background: repeating-linear-gradient(0deg, rgb(221, 221, 221) 0px, rgb(221, 221, 221) 1px,transparent 1px, transparent 21px),repeating-linear-gradient(90deg, rgb(221, 221, 221) 0px, rgb(221, 221, 221) 1px,transparent 1px, transparent 21px),linear-gradient(90deg, hsl(104,0%,96%),hsl(104,0%,96%));
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}
.toolbar-button{
    min-width: 37px;
    min-height: 31px;
}
</style>
