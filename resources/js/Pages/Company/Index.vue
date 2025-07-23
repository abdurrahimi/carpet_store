<template>
    <div>

        <Head title="Company" />
        <div class="text-right">
            <transition name="fade">
                <button :class="selectedIds.length > 0 ? 'show' : ''" @click="deleteSelected"
                    class="btn btn-danger mr-2 mb-2 fade">
                    <i class="fa fa-trash"></i>&nbsp;Delete Bulk
                </button>
            </transition>
            <button class="btn btn-primary mb-2" type="button" @click="createData">+ Data Baru</button>
        </div>

        <div v-if="$page.props.flash.success" class="alert alert-success" role="alert">{{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">{{ $page.props.flash.error }}</div>

        <div class="card shadow">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-building"></i>&nbsp;Data Perusahaan
                </h4>
            </div>
            <Table :columns="table" :rows="data" @update:selectedRows="handleSelectedRows">
                <template #actions="{ row }">
                    <button @click="editRow(row)" class="btn btn-warning btn-sm" title="Edit">Edit</button>&nbsp;
                    <button @click="deleteRow(row)" class="btn btn-danger btn-sm" title="Delete">Delete</button>
                </template>
            </Table>

            <Modal :show="modalShow" :title="modalTitle" id="modal-add">
                <form ref="formCompany" @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Perusahaan</label>
                            <input type="text" id="name" v-model="form.name" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.name }" required>
                            <span class="text-danger">{{ $page?.props?.errors?.name }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Kode Perusahaan</label>
                            <input type="text" id="code" v-model="form.code" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.code }" required>
                            <span class="text-danger">{{ $page?.props?.errors?.code }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Alamat Perusahaan</label>
                            <textarea type="text" id="address" v-model="form.address" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.address }" required></textarea>
                            <span class="text-danger">{{ $page?.props?.errors?.address }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" id="phone" v-model="form.phone_number" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.phone_number }">
                            <span class="text-danger">{{ $page?.props?.errors?.phone_number }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" id="email" v-model="form.email" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.email }">
                            <span class="text-danger">{{ $page?.props?.errors?.email }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bank" class="form-label">Bank</label>
                            <input type="text" id="bank" v-model="form.bank_name" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.bank_name }">
                            <span class="text-danger">{{ $page?.props?.errors?.bank_name }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="account" class="form-label">No. Rekening</label>
                            <input type="text" id="account" v-model="form.bank_account_number" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.bank_account_number }">
                            <span class="text-danger">{{ $page?.props?.errors?.bank_account_number }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="holder" class="form-label">Atas Nama</label>
                            <input type="text" id="holder" v-model="form.bank_account_holder" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.bank_account_holder }">
                            <span class="text-danger">{{ $page?.props?.errors?.bank_account_holder }}</span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" id="logo" class="form-control" @change="handleFileUpload">
                            <span class="text-danger">{{ $page?.props?.errors?.logo }}</span>
                            <div v-if="form.preview" class="mt-2">
                                <img :src="form.preview" height="100" />
                            </div>
                        </div>
                    </div>
                </form>

                <template #footer>
                    <button type="button" class="btn btn-primary" :class="{ disabled: form.submit }"
                        @click="submitForm">
                        <template v-if="form.submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span>Loading</span>
                        </template>
                        <span v-else>Save</span>
                    </button>
                </template>
            </Modal>
        </div>
    </div>
</template>

<script lang="jsx">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";

export default {
    layout: AuthenticatedLayout,
    props: ["datas"],
    components: {
        Head,
        Modal,
    },
    data() {
        return {
            table: [
                {
                    title: "Created At",
                    data: "phone_number",
                    render: (row) => <img src={row.logo}/>
                },
                { title: "Nama", data: "name" },
                { title: "Telepon", data: "phone_number" },
                { title: "Bank", data: "bank_name" },
                { title: "No. Rekening", data: "bank_account_number" },
                { title: "Atas Nama", data: "bank_account_holder" },
                
            ],
            data: this.datas,
            selectedIds: [],
            modalShow: false,
            modalTitle: "",
            form: {},
        };
    },
    mounted() {
        this.resetForm();
    },
    methods: {
        createData() {
            this.$page.props.errors = null;
            this.resetForm();
            this.modalTitle = "Tambah Company";
            $("#modal-add").modal("show");
        },
        editRow(row) {
            this.$page.props.errors = null;
            this.resetForm(row);
            this.modalTitle = "Edit Company";
            $("#modal-add").modal("show");
        },
        resetForm(data = {}) {
            this.form = {
                id: data.id || "",
                name: data.name || "",
                phone_number: data.phone_number || "",
                email: data.email || "",
                bank_name: data.bank_name || "",
                bank_account_number: data.bank_account_number || "",
                bank_account_holder: data.bank_account_holder || "",
                logo: null,
                code: data.code || "",
                address: data.address || "",
                preview: data.logo_url || null,
                submit: false,
            };
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.logo = file;
                this.form.preview = URL.createObjectURL(file);
            }
        },
        submitForm() {
            if (this.form.submit) return;
            console.log(this.form)
            this.form.submit = true;
            const formData = new FormData();

            for (const key in this.form) {
                if (key === "submit" || key === "preview") continue;
                if (this.form[key] !== null) formData.append(key, this.form[key]);
            }

            router.post(
                this.form.id ? this.route("company.update", this.form.id) : this.route("company.store"),
                formData,
                {
                    forceFormData: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.submit = false;
                        $("#modal-add").modal("hide");
                        this.$success("Data berhasil disimpan");
                        router.visit(this.$page.url, { only: ["users"] });
                    },
                    onError: () => {
                        this.form.submit = false;
                        this.$error();
                    },
                }
            );
        },
        deleteRow(row) {
            this.$confirm("Yakin ingin hapus?", () => this.deleteAction(row), false);
        },
        deleteAction(row) {
            router.post(this.route("company.delete"), { ids: [row.id] }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$success("Data berhasil dihapus");
                    router.visit(this.$page.url, { only: ["users"] });
                },
                onError: () => {
                    this.$error();
                },
            });
        },
        handleSelectedRows(rows) {
            this.selectedIds = rows;
        },
        deleteSelected() {
            this.$confirm("Yakin hapus semua data terpilih?", this.deleteSelectedAction, false);
        },
        deleteSelectedAction() {
            router.post(this.route("company.delete"), { ids: this.selectedIds }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$success("Data berhasil dihapus");
                    router.visit(this.$page.url, { only: ["users"] });
                },
                onError: () => {
                    this.$error();
                },
            });
        },
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
