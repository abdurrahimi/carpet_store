<template>
    <Modal :show="modalUploadShow" :title="modalTitle" id="modal-product-upload">
        <form ref="formUpload" @submit.prevent="submitUpload">
            <input type="file" ref="file" @change="handleFileUpload" />
        </form>
        <template #footer>
            <button type="button" class="btn btn-primary" :class="{ disabled: form.submit }" @click="submitUpload">
                <div v-if="form.submit">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="bott">Loading</span>
                </div>
                <span v-else class="bott">Save</span>
            </button>
        </template>
    </Modal>
</template>
<script>
import Modal from '@/Components/Modal.vue'
import Multiselect from "vue-multiselect";
import { router } from "@inertiajs/vue3";
export default {
    components: {
        Modal,
        Multiselect,
    },
    data() {
        return {
            modalUploadShow: true,
            modalTitle: "Upload Data Product",
            form: {
                file: null,
                submit: false
            }
        }
    },
    mounted() {
        this.form = {
            file: null,
            submit: false
        }
    },
    methods: {
        handleFileUpload(event) {
            this.form.file = event.target.files[0];
        },
        submitUpload() {
            const formData = new FormData();
            formData.append("file", this.form.file);
            if (this.form.submit) {
                return;
            }

            this.form.submit = true;
            router.post(route("product.import"), formData, {
                onFinish: () => {
                    this.$success("Data berhasil disimpan");
                    router.visit(this.$page.url, {
                        only: ["products"],
                    });
                    this.form.submit = false;
                    $("#modal-product-upload").modal("hide");
                },
                onError: () => {
                    this.form.submit = false;
                    this.$error();
                },
            });
        }
    }
}
</script>