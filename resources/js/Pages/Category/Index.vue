<template>
    <div>

        <Head title="Kategori Produk" />
        <div class="text-right">
            <transition name="fade">
                <button :class="selectedIds.length > 0 ? 'show' : ''" @click="deleteSelected"
                    class="btn btn-danger mr-2 mb-2 fade">
                    <i class="fa fa-trash"></i>&nbsp;Delete Bulk
                </button>
            </transition>
            <button class="btn btn-primary mb-2" type="button" @click="createData">
                + Data Baru
            </button>
        </div>
        <div v-if="$page.props.flash.success" class="alert alert-success" role="alert">
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
            {{ $page.props.flash.error }}
        </div>
        <div class="card shadow">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-user"></i>&nbsp;Data Kategori Product
                </h4>
            </div>
            <Table :columns="table" :rows="data" @update:selectedRows="handleSelectedRows">
                <template #actions="{ row }">
                    <button @click="editRow(row)" class="btn btn-warning btn-sm" title="Edit">
                        Edit</button>&nbsp;
                    <button @click="deleteRow(row)" class="btn btn-danger btn-sm" title="Delete">
                        Delete
                    </button>
                </template>
            </Table>

            <Modal :show="modalShow" :title="modalTitle" id="modal-add">
                <form ref="formCustomer" @submit.prevent="submitForm">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" v-model="form.name" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.name }" placeholder="Enter Name"
                                required />
                            <span class="text-danger">{{ $page?.props?.errors?.name }}</span>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Satuan Penjualan</label>
                            <select id="type" v-model="form.tipe_penjualan" class="form-control"
                                :class="{ 'is-invalid': $page?.props?.errors?.tipe_penjualan }" required>
                                <option value="">Select Type</option>
                                <option value="1">Satuan</option>
                                <option value="2">Meteran</option>
                            </select>
                            <span class="text-danger">{{ $page?.props?.errors?.tipe_penjualan }}</span>
                        </div>

                        <!-- Color -->
                        <div class="col-md-12 mb-3"
                            style="background: #fafafa; padding: 10px; border-radius: 5px; box-shadow: 2px 2px 12px 1px;">
                            <div class="col-md-12 mb-3" v-for="(_, key) in form.color">
                                <label for="name" class="form-label">Color</label>
                                <input type="text" id="name" v-model="form.color[key].name" class="form-control"
                                    :class="{ 'is-invalid': $page?.props?.errors?.color[key]?.name }"
                                    placeholder="Enter Name" required />
                                <a href="#" @click="form.color.splice(key, 1)" class="text-danger">Remove</a>
                                <span class="text-danger float-right">{{ $page?.props?.errors?.color[key]?.name
                                    }}</span>
                            </div>
                            <button type="button" class="btn btn-primary ml-2 btn-sm"
                                @click="form.color.push({ id: null, name: '' })">Add
                                Color</button>
                        </div>
                    </div>
                </form>

                <template #footer>
                    <button type="button" class="btn btn-primary" :class="{ disabled: form.submit }"
                        @click="submitForm">
                        <template v-if="form.submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="bott">Loading</span>
                        </template>
                        <span v-else class="bott">Save</span>
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
                    title: "Category",
                    data: "name",
                },
                {
                    title: "Satuan Penjualan",
                    data: "tipe_penjualan",
                    render: (data) => {
                        switch (data.tipe_penjualan) {
                            case 1: {
                                return <span>Satuan</span>;
                            }
                            case 2: {
                                return <span>Meteran</span>;
                            }
                        }
                    }
                },

                {
                    title: "Created At",
                    data: "created_at",
                    render: (row) => <span>{this.$timeFormat(row.created_at)}</span>
                },
                {
                    title: "Created By",
                    data: "creator.name",
                },
            ],
            data: this.datas,
            selectedIds: [],
            form: {},
            stores: [],
            modalShow: false,
            modalTitle: "",
            errors: {},
        }
    },
    mounted() {
        this.resetForm();
    },
    methods: {
        createData() {
            this.$page.props.errors = null;
            this.resetForm();
            this.modalTitle = "Tambah Data Kategori Produk";
            $("#modal-add").modal("show");
        },
        editRow(row) {
            this.$page.props.errors = null;
            this.resetForm(row);
            this.modalTitle = "Ubah Data Kategori Produk";
            $("#modal-add").modal("show");
        },
        async submitForm() {
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
        deleteRow(row) {
            this.$confirm(
                "You won't be able to revert this!",
                () => this.deleteAction(row),
                false
            );
        },
        deleteAction(row) {
            router.post(this.route('category.delete'), { ids: [row.id] }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$success("Data berhasil dihapus");
                    router.visit(this.$page.url, {
                        only: ["users"],
                    });
                },
                onError: () => {
                    this.$error();
                },
            });
        },
        handleSelectedRows(selectedRows) {
            this.selectedIds = selectedRows;
        },
        deleteSelected() {
            this.$confirm(
                "You won't be able to revert this!",
                () => this.deleteSelectedAction(),
                false
            );
        },
        deleteSelectedAction() {
            router.post(this.route('category.delete'), { ids: this.selectedIds }, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$success("Data berhasil dihapus");
                    router.visit(this.$page.url, {
                        only: ["users"],
                    });
                },
                onError: () => {
                    this.$error();
                },
            });
        },
        resetForm(data = null) {
            console.log(data)
            this.form = {
                id: data?.id ?? "",
                name: data?.name ?? "",
                tipe_penjualan: data?.tipe_penjualan ?? 1,
                color: data?.color ?? [{ id: null, name: "" }],
            }
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