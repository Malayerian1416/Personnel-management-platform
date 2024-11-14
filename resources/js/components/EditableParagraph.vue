<template>
    <div class="position-relative">
        <p :id="id" class="line" :class="css_class" data-bs-toggle="popover" data-bs-title="تنظیمات جعبه متن" data-bs-content="" contenteditable="false" v-on:input="InputText" v-on:keydown="KeyDown" v-on:dblclick="EditText" v-on:mouseover="MouseMoveIn" v-on:mouseout="MouseMoveOut" v-on:click="SelectParagraph" :style="css_style">
            {{ content }}
        </p>
    </div>
</template>

<script>
export default {
    name: "EditableParagraph",
    props: ["id","content","css_style","css_class"],
    data(){
        return{
            draggable : "",
            contextmenu : "",
            isMouseHover : false,
            tested: false,
            popoverOptions: {
                html: true,
                trigger: 'hover',
                content: `<ul>
                            <li>جابجایی جعبه متن با فشرده نگه داشتن کلید Ctrl</li>
                            <li>تغییر اندازه جعبه متن با فشرده نگه داشتن کلید Shift</li>
                            <li>ویرایش جعبه متن با دوبار کلیک</li>
                            <li>ایجاد خط جدید در حالت ویرایش متن Shift + Enter</li>
                          </ul>`
            }
        }
    },
    mounted() {
        const self = this;
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl,self.popoverOptions));
        const element = document.getElementById(self.id);
        this.$emit("ElementSelected",this.id);
        this.draggable = new Draggabilly(element, {
            containment: '.template-page',
            grid: [5, 5]
        });
        this.draggable.on( 'pointerUp', function( event, pointer ) {
            let paragraph = self.$parent.$data.paragraphs.find(paragraph => {
                return paragraph.id === self.id
            });
            paragraph.top = self.draggable.position.y;
            paragraph.left = self.draggable.position.x;
            self.$parent.$forceUpdate();
        });
        this.draggable.disable();
        document.getElementById(self.id).focus();
        document.addEventListener("click",(e) => {
            if (!element.contains(e.target))
                element.setAttribute("contenteditable", "false");
        })
        document.addEventListener("keyup",function (){
            if (self.$parent.$data.selected === self.id){
                self.draggable.disable();
                element.style.cursor = "default";
                $(element).resizableSafe('destroy');
            }
        });
        document.addEventListener("keydown",function (e){
            const element = document.getElementById(self.id);
            if (e.altKey)
                e.preventDefault();
            else if (e.ctrlKey && self.$parent.$data.selected === self.id) {
                element.style.cursor = "move";
                self.draggable.enable();
            }
            else if (e.shiftKey && self.$parent.$data.selected === self.id)
                $(element).resizableSafe({
                    resizeWidthFrom: 'right',
                    onDragEnd: function (e, $el, opt) {
                        let paragraph = self.$parent.$data.paragraphs.find(paragraph => {
                            return paragraph.id === self.id
                        });
                        paragraph.width = document.getElementById(self.id).style.width;
                        paragraph.height = document.getElementById(self.id).style.height;
                        self.$parent.$forceUpdate();
                    }
                });
            else{
                self.draggable.disable();
                element.style.cursor = "default";
                $(element).resizableSafe('destroy');
            }
        });
        element.addEventListener("paste", function(e) {
            e.preventDefault();
            let text = (e.originalEvent || e).clipboardData.getData('text/plain');
            element.innerText += text;
            let paragraph = self.$parent.$data.paragraphs.find(paragraph => {
                return paragraph.id === self.id
            });
            paragraph.text = element.innerText;
            self.$parent.$forceUpdate();
        });
    },
    methods: {
        KeyDown(e){
            if (e.key === "Enter" && !e.shiftKey)
                e.preventDefault();
        },
        InputText(e){
            const self = this;
            let paragraph = self.$parent.$data.paragraphs.find(paragraph => {
                return paragraph.id === self.id
            });
            paragraph.text = e.target.innerText;
            self.$parent.$forceUpdate();
        },
        MouseMoveIn(){
            if (this.$parent.$data.selected === this.id) {
                this.isMouseHover = true;
                this.$forceUpdate();
            }

        },
        MouseMoveOut(){
            if (this.$parent.$data.selected === this.id) {
                this.isMouseHover = false;
                this.$forceUpdate();
            }
        },
        EditText(e){
            if (this.$parent.$data.selected === this.id) {
                e.currentTarget.style.cursor = "text";
                $(e.currentTarget).removeClass("resizable");
                e.currentTarget.setAttribute("contenteditable", true);
                e.currentTarget.focus();
            }
        },
        SelectParagraph(){
            this.$emit("ElementSelected",this.id);
        }
    }
}
</script>
