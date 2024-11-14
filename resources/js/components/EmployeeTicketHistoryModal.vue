<template>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title iransans">
                سوابق تیکت ها
            </h5>
        </div>
        <div class="modal-body">
            <reference-box :refs_needed="kind === 'individual' ? [4] : [1,2,3]" @reference_selected="ReferenceSetup" @reference_check="ReferenceChecked"></reference-box>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">عناوین تیکت ها</label>
                </span>
                <div class="fieldset-body">
                    <select id="subjects" class="form-control iransans" data-size="10" data-live-search="true" v-on:change="LoadChats">
                        <option v-for="room in AllTickets" :key="room.id" :value="room.id">{{ room.subject }}</option>
                    </select>
                </div>
            </div>
            <div class="fieldset">
                <span class="legend">
                    <label class="iransans">مکالمات انجام شده</label>
                </span>
                <div class="fieldset-body">
                    <div class="w-100" v-for="chat in chats" :key="chat.id">
                        <div v-if="chat.sender === 'expert'" class="p-3 d-flex flex-row align-items-center justify-content-start gap-4">
                            <img src="/images/ticket_support.png" alt="همیاران">
                            <div class="chat-bubble gap-1 gap-lg-2">
                                <p class="iransans text-muted mb-0">{{`${chat.expert.name} (${chat.expert.role.name})`}}</p>
                                <p class="iransans bold-font mb-0" style="text-align: justify;word-break: break-word;">{{chat.message}}</p>
                                <a v-if="chat.attachment" download :href="`/storage/ticket_attachments/${chat.room_id}/${chat.attachment}`" class="iransans">
                                    <i class="fa fa-paperclip fa-1-2x vertical-middle"></i>
                                    <span>دانلود فایل ضمیمه</span>
                                </a>
                                <div class="w-100 d-flex flex-row align-items-center justify-content-end">
                                    <span class="iranyekan text-muted">{{PersianDate(chat.updated_at)}}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-3 d-flex flex-row align-items-center justify-content-end gap-4">
                            <div class="chat-bubble-invert gap-1 gap-lg-2">
                                <p class="iransans text-muted mb-0">{{`${chat.employee.name} (${chat.employee.national_code})`}}</p>
                                <p class="iransans bold-font mb-0" style="text-align: justify;word-break: break-word;">{{chat.message}}</p>
                                <a v-if="chat.attachment" download :href="`/storage/ticket_attachments/${chat.room_id}/${chat.attachment}`" class="iransans">
                                    <i class="fa fa-paperclip fa-1-2x vertical-middle"></i>
                                    <span>دانلود فایل ضمیمه</span>
                                </a>
                                <div class="w-100 d-flex flex-row align-items-center justify-content-end">
                                    <span class="iranyekan text-muted">{{PersianDate(chat.updated_at)}}</span>
                                </div>
                            </div>
                            <img src="/images/ticket_user.png" alt="همیاران">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-menu">
            <button type="button" class="btn btn-outline-secondary iransans" data-bs-dismiss="modal" v-on:click="$root.$data.employee_operation_type=''">
                <i class="fa fa-times fa-1-2x me-1"></i>
                <span class="iransans">بستن</span>
            </button>
        </div>
    </div>
</template>

<script>
import route from "ziggy-js";
import alertify from "alertifyjs";

export default {
    name: "EmployeeTicketHistoryModal",
    props:["kind"],
    data(){
        return {
            reference: null,
            data: null,
            message: "",
            AllTickets: [],
            chats: []
        }
    },
    mounted() {
        const self = this;
        let subjects = $("#subjects");
        self.$root.$data.show_loading = true;
        axios.post(route("EmployeesManagement.get_tickets"), {"id" : this.data})
            .then(function (response) {
                self.$root.$data.show_loading = false;
                switch (response.data["result"]) {
                    case "success": {
                        self.AllTickets = response.data.AllTickets;
                        self.$nextTick(() => {
                            subjects.selectpicker();
                        });
                        if (self.AllTickets.length > 0)
                            self.chats = self.AllTickets[0].tickets;
                        alertify.notify(response.data["message"], 'success', "5");
                        break;
                    }
                    case "fail": {
                        alertify.notify(response.data["message"], 'error', "30");
                        break;
                    }
                }
            }).catch(function (error) {
            self.$root.$data.show_loading = false;
            alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'error', "30");
        });
    },
    methods:{
        ReferenceChecked(ref){
            this.reference = ref;
        },
        ReferenceSetup(ref){
            this.reference = ref.type;
            this.data = ref.target;
        },
        PersianDate(date){
            return new persianDate(new Date(date)).format("HH:mm:ss YYYY/MM/DD")
        },
        LoadChats(e){
            const self = this;
            const room_id = parseInt(e.target.value);
            let room = self.AllTickets.find(room => {
                return room.id === room_id;
            });
            self.chats = room.tickets;
        },
    }
}
</script>

<style scoped>

</style>
