<template>
    <button id="resend_code" :form="form" type="submit" :disabled="status" :class="css">
        <span v-if="!loading" v-show="!status" style="font-size: 1rem">ارسال مجدد</span>
        <i v-show="loading" class="fas fa-spinner fa-pulse fa-1-2x ms-2"></i>
        <i v-if="!loading" v-show="!status" class="fa fa-refresh fa-1-2x ms-2"></i>
        <span id="resend_code_second" class="text-center" v-show="status" style="letter-spacing: 2px;font-size: 1rem"></span><span v-show="status" id="resend_code_sep" class="text-center mr-2 ml-2 mr-2" style="font-size: 1rem">:</span><span v-show="status" id="resend_code_minute" class="text-center" style="letter-spacing: 2px;font-size: 1rem"></span>
    </button>
</template>

<script>
export default {
    name: "TimeCounterButton",
    props:["css","seconds","limit","form"],
    data(){
        return{
            second: this.seconds,
            loading: false,
            end: this.limit,
            interval: ''
        }
    },
    computed:{
        status:{
            get(){
                return this.second < this.limit;
            },
            set(value){
                return value;
            }
        }
    },
    watch:{
        second(before,after) {
            if (after >= this.limit)
                this.status = false;
        }
    },
    methods:{
        time() {
            let minute,second;
            if (this.second < this.end) {
                minute = Math.floor((this.end - Number(this.second)) / 60);
                second = (this.end - Number(this.second)) % 60;
                minute = minute < 10 ? '0' + minute.toString() : minute.toString();
                second = second < 10 ? '0' + second.toString() : second.toString();
            }
            else {
                clearInterval(this.interval);
                minute = "00";
                second = "00";
                this.status = false;
            }
            return {"minute":minute,"second":second};
        },
        start(){
            this.interval = setInterval(() => {
                ++this.second;
                let time = this.time();
                $("#resend_code_second").text(time["second"]);
                $("#resend_code_minute").text(time["minute"]);
            },1000);
        }
    },
    mounted() {
       this.start();
    }
}
</script>
