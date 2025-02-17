<template>
    <div>

        <Head title="Approval" />
        <div v-if="$page.props.flash.success" class="alert alert-success" role="alert">
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
            {{ $page.props.flash.error }}
        </div>
        <div class="card shadow">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-user"></i>&nbsp;Data Approval
                </h4>
            </div>
            <Table :columns="table" :rows="data">
                <template #actions="{ row }">
                    <button @click="detail(row)" class="btn btn-warning btn-sm" title="Edit">
                        Detail</button>&nbsp;
                </template>
            </Table>
        </div>
        <Modal :show="modalShow" title="Data Approval" id="modal-add">
            <template #footer>
                <button type="button" class="btn btn-primary"
                    :class="{ disabled: form.submit, disabled: form.status != 0 }" @click="approveData">
                    <template v-if="form.submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="bott">Loading</span>
                    </template>
                    <span v-else class="bott">Approve</span>
                </button>
            </template>
        </Modal>
    </div>
</template>
<script lang="jsx">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.css";
export default {
    layout: AuthenticatedLayout,
    props: ["datas"],
    components: {
        Head,
        Modal,
        Multiselect,
    },
    data() {
        return {
            table: [
                
                {
                    title: "Requestor",
                    data: "requestor.name",
                },
                {
                    title: "Detail",
                    data: "detail",
                },
                {
                    title: "Status",
                    data: "status",
                    render: (row) => {
                        switch (row.status) {
                            case 0:
                                return <span>Pending</span>;
                            case 1:
                                return <span>Approved</span>;
                            case 2:
                                return <span>Rejected</span>;
                            default:
                                return <span>Unknown</span>;
                        }
                    },
                },

                {
                    title: "Created At",
                    data: "created_at",
                },
                {
                    title: "Created By",
                    data: "creator.name",
                },
            ],
            data: this.datas,
            selectedIds: [],
            errors: {},
            form: {},
        }
    },
    mounted() {
        this.resetForm();
    },
    methods: {
        detail(row) {

        },
        async approveData() {
            if (this.form.submit) {
                return;
            }

            this.form.submit = true;
            router.post(
                this.form.id
                    ? this.route('category.update', this.form.id)
                    : this.route('category.store'),
                this.form,
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.submit = false;
                        $("#modal-add").modal("hide");
                        this.$success("Data berhasil disimpan");
                        router.visit(this.$page.url, {
                            only: ["users"],
                        });
                    },
                    onError: () => {
                        this.form.submit = false;
                        this.$error();
                    },
                });
        },
        resetForm(row = null) {
            this.form = {
                id: row?.id ?? null,
                requestor: row?.requestor ?? {},
                approver: row?.approver ?? {},
                detail: row?.detail ?? "",
                status: 0,
                submit: false,
            };
            this.errors = {};
        }
    },
};
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to

/* .fade-leave-active in <2.1.8 */
    {
    opacity: 0;
}
</style>