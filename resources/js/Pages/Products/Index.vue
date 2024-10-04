<template>
    <div>
        <Head title="Products" />
        <div class="text-right">
            <transition name="fade">
                <button
                    :class="selectedIds.length > 0 ? 'show' : ''"
                    @click="deleteSelected"
                    class="btn btn-danger mr-2 mb-2 fade"
                >
                    <i class="fa fa-trash"></i>&nbsp;Delete Bulk
                </button>
            </transition>
            <button
                class="btn btn-primary mb-2"
                type="button"
                @click="createProduct"
            >
                + Data Baru
            </button>
            &nbsp;
            <button
                class="btn btn-success mb-2"
                type="button"
                @click="uploadProduct"
            >
                + Upload
            </button>
        </div>
        <div
            v-if="$page.props.flash.success"
            class="alert alert-success"
            role="alert"
        >
            {{ $page.props.flash.success }}
        </div>
        <div
            v-if="$page.props.flash.error"
            class="alert alert-danger"
            role="alert"
        >
            {{ $page.props.flash.error }}
        </div>
        <div class="card shadow">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-box"></i>&nbsp;Data Product
                </h4>
            </div>
            <Table
                :columns="table"
                :rows="data"
                @update:selectedRows="handleSelectedRows"
            >
                <template #actions="{ row }">
                    <button
                        @click="detailRow(row)"
                        class="btn btn-info btn-sm"
                        title="Detail"
                    >
                        Detail
                    </button>&nbsp;
                    <button
                        @click="editRow(row)"
                        class="btn btn-warning btn-sm"
                        title="Edit"
                    >
                        Edit</button
                    >&nbsp;
                    <button
                        @click="deleteRow(row)"
                        class="btn btn-danger btn-sm"
                        title="Delete"
                    >
                        Delete
                    </button>
                </template>
            </Table>
        </div>
    </div>
    <ModalUpload/>
    <ModalForm :modalTitle="modalTitle" :type="type" :editData="rowData" :toEdit="toEdit"/>
</template>
<script lang="jsx">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ModalUpload from "@/Pages/Products/ModalUpload.vue";
import ModalForm from "@/Pages/Products/ModalForm.vue";
import { Head, router } from "@inertiajs/vue3";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.css";
export default {
    layout: AuthenticatedLayout,
    props: ["products", "categories", "supplier"],
    components: {
        Head,
        ModalUpload,
        ModalForm,
        Multiselect,
    },
    data() {
        return {
            table: [
                {
                    title: "ORI DESIGN ID",
                    data: "id",
                },
                {
                    title: "NEW DESIGN (SKU)",
                    data: "sku",
                },
                {
                    title: "PRODUCT CATEGORY",
                    data: "category",
                },
                {
                    title: "DESIGN NAME",
                    data: "design_name",
                },
                {
                    title: "COLOR",
                    data: "color",
                },
                {
                    title: "PATTERN",
                    data: "pattern",
                },
                {
                    title: "TYPE",
                    data: "origin",
                    render: (row) => {
                        return row.origin == 1 ? (
                            <span>Import</span>
                        ) : (
                            <span>Local</span>
                        );
                    },
                }
            ],
            data: this.products,
            modal: {
                upload: false,
                form: false,
            },
            toEdit: true,
            selectedIds: [],
            modalTitle: "",
            type: "create",
            rowData: {},
        };
    },
    methods: {
        handleSelectedRows(row){
            this.selectedIds = row
        },
        createProduct() {
            this.rowData = {}
            this.modalTitle = "Tambah Data Product";
            $("#modal-product-index").modal("show");
        },
        uploadProduct() {
            $("#modal-product-upload").modal("show");
        },
        detailRow(row) {
            this.rowData = row
            this.toEdit = false;
            this.modalTitle = "Detail Data Product";
            $("#modal-product-index").modal("show");
        },
        editRow(row) {
            this.rowData = row
            this.toEdit = true;
            this.modalTitle = "Ubah Data Product";
            $("#modal-product-index").modal("show");
        },
        deleteRow(row) {
            this.$confirm(
                "You won't be able to revert this!",
                () => this.deleteAction(row),
                false
            );
        },
        deleteAction(row) {
            router.delete(this.route("products.delete", row.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.$success("Data berhasil dihapus");
                    router.visit(this.$page.url, {
                        only: ["products"],
                    });
                },
                onError: () => {
                    this.$error();
                },
            });
        }
    }
};
</script>
