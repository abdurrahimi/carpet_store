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
        <Modal :show="modalShow" title="Data Approval" id="modal-approval">
            <div class="bg-gray-100 p-4">
                <h2>{{ form.title }}</h2>
                <i>Waktu Permintaan : {{ localDate(form.created_at) }}</i><br><br>
                <p>{{ form.detail }}</p>
            
            </div>
            <template #footer>
                <button type="button" class="btn btn-primary"
                    :class="{ disabled: form.submit, disabled: form.status != 0 }" @click="approveData(1)">
                    <template v-if="form.submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="bott">Loading</span>
                    </template>
                    <span v-else class="bott">Approve</span>
                </button>
                <button type="button" class="btn btn-danger"
                    :class="{ disabled: form.submit, disabled: form.status != 0 }" @click="approveData(0)">
                    <template v-if="form.submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="bott">Loading</span>
                    </template>
                    <span v-else class="bott">Reject</span>
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
                    title: "Status",
                    data: "status",
                    render: (row) => {
                        switch (row.status) {
                            case 0:
                                return <span class={"badge badge-warning"}>Waiting for Approval</span>;
                            case 1:
                                return <span class={"badge badge-success"}>Approved</span>;
                            case 2:
                                return <span class={"badge badge-danger"}>Rejected</span>;
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
            this.form = row;
            $("#modal-approval").modal("show");
        },
        async approveData(type) {
            if (this.form.submit) {
                return;
            }

            this.form.type = type;

            this.form.submit = true;
            router.post(
                this.route('approval.action'),
                this.form,
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.submit = false;
                        $("#modal-approval").modal("hide");
                        this.$success("Data berhasil disimpan");
                        router.visit(this.$page.url, {
                            only: ["approval"],
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
        },
        localDate(date){
            return new Date(date).toLocaleString();
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

h2{
    font-size: 1.5rem;
    font-weight: 600;
    color: black;
}
p{
    color: black;
}
</style>