<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <span id="texto"></span>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'RelacionLocal',
    data () {
        return {
            id: 0
        }
    },
    methods: {
        showModal (attr) {
            let me = this
            me.id = attr
            $('#texto').text(me.id)
            // alert (me.id)
            // me.mostrarModal = ne.isVisible()

            // alert('....' + me.id)
            // if (me.id !== 'undefined') {
                $('#exampleModal').modal('show')
            // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        }
    },
    created () {
    },
    activated: function () {
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        this.$route.meta.auth = localStorage.getItem('autenticado')
        
    },
    deactivated: function () {
    },
    mounted () {
    },
    beforeDestroy: function () {
        // this.$store.state.mostrarModal = false
    }
}
</script>