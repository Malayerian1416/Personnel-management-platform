<template>
    <div>
        <div class="dropdown position-relative table-functions iransans">
            <a class="table-functions-button dropdown-toggle border-0 iransans info-color" data-bs-display="static" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-cog fa-1-2x"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TableOperations",
    mounted() {
        $(".table-functions").on('show.bs.dropdown', function () {
            let table = document.getElementsByClassName('table-scroll');
            let menu = $(this).closest("td").find(".dropdown-menu");
            $(table).css("min-height",((parseInt($(menu).css("height")) / 2) + parseInt($(table).css("height"))).toString() + "px");
        }).on('hide.bs.dropdown', function () {
            $('.table-scroll').css("min-height","auto");
        });
    }
}
</script>
