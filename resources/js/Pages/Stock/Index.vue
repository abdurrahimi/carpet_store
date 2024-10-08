<template>
    <div>
        <Head title="Data Stok" />
        <div class="text-right">
            <transition name="fade">
                <button
                    :class="selectedIds.length > 0 ? 'show' : ''"
                    @click="deleteSelected"
                    class="btn btn-danger mr-2 mb-2 fade"
                >
                    <i class="fa fa-trash"></i>&nbsp;Hapus Pilihan
                </button>
            </transition>
            <button
                class="btn btn-primary mb-2"
                type="button"
                @click="createStock"
            >
                + Stok Baru
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
                    <i class="fa fa-box"></i>&nbsp;Data Stok
                </h4>
            </div>
            <Table
                :columns="table"
                :rows="data"
                @update:selectedRows="handleSelectedRows"
            >
                <template #actions="{ row }">
                    <button
                        @click="deleteRow(row)"
                        class="btn btn-danger btn-sm"
                        title="Delete"
                    >
                        Delete</button
                    >&nbsp;
                </template>
            </Table>
        </div>
        <Modal :show="modalShow" :title="modalTitle" id="modal-stock-index">
            <form ref="formStock" @submit.prevent="handleSubmit">
                <div class="row">
                    <!-- Produk -->
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label"
                            >Pilih Produk</label
                        >
                        <Multiselect
                            v-model="form.product"
                            :options="products"
                            track-by="id"
                            label="name"
                            @search-change="getProducts"
                            :internal-search="false"
                            :class="{
                                'is-invalid': $page?.props?.errors?.product?.id,
                            }"
                        ></Multiselect>
                        <span class="text-danger">{{
                            $page?.props?.errors?.product?.id
                        }}</span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="toko_id" class="form-label">Store</label>
                        <Multiselect
                            v-model="form.store"
                            :options="store"
                            track-by="id"
                            label="name"
                            @search-change="getDataStore"
                            :internal-search="false"
                            :class="{
                                'is-invalid': $page?.props?.errors?.store?.id,
                            }"
                        ></Multiselect>
                        <span class="text-danger">{{
                            $page?.props?.errors?.store?.id
                        }}</span>
                    </div>

                    <!-- Supplier -->
                    <div class="col-md-6 mb-3">
                        <label for="toko_id" class="form-label">Supplier</label>
                        <Multiselect
                            v-model="form.supplier"
                            :options="supplier"
                            track-by="id"
                            label="name"
                            @search-change="getDataSupplier"
                            :internal-search="false"
                            :class="{
                                'is-invalid':
                                    $page?.props?.errors?.supplier?.id,
                            }"
                        ></Multiselect>
                        <span class="text-danger">{{
                            $page?.props?.errors?.supplier?.id
                        }}</span>
                    </div>

                    <!-- Jumlah -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="total">Jumlah</label>
                            <input
                                type="number"
                                v-model="form.total"
                                class="form-control"
                                id="total"
                                :class="{
                                    'is-invalid': $page?.props?.errors?.total,
                                }"
                                required
                            />
                            <div
                                v-if="$page?.props?.errors?.total"
                                class="invalid-feedback"
                            >
                                {{ $page?.props?.errors?.total }}
                            </div>
                        </div>
                    </div>

                    <!-- Tipe Produk -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="product_type">Tipe Produk</label>
                            <select
                                v-model="form.product_type"
                                class="form-control"
                                id="product_type"
                                :class="{
                                    'is-invalid':
                                        $page?.props?.errors?.product_type,
                                }"
                            >
                                <option value="1">Baru</option>
                                <option value="2">Bekas</option>
                            </select>
                            <div
                                v-if="$page?.props?.errors?.product_type"
                                class="invalid-feedback"
                            >
                                {{ $page?.props?.errors?.product_type }}
                            </div>
                        </div>
                    </div>

                    <!-- Panjang -->
                    <div class="col-md-6 mb-3" v-if="form.product_type == 2">
                        <div class="form-group">
                            <label for="value">Sisa Panjang(Meter)</label>
                            <input
                                type="number"
                                v-model="form.panjang"
                                class="form-control"
                                id="value"
                                :class="{
                                    'is-invalid': $page?.props?.errors?.panjang,
                                }"
                                required
                            />
                            <div
                                v-if="$page?.props?.errors?.panjang"
                                class="invalid-feedback"
                            >
                                {{ $page?.props?.errors?.panjang }}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <button
                    type="button"
                    class="btn btn-primary"
                    :class="{ disabled: form.submit }"
                    @click="submitForm"
                >
                    <div v-if="form.submit">
                        <span
                            class="spinner-border spinner-border-sm"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        <span class="bott">Loading</span>
                    </div>
                    <span v-else class="bott">Simpan</span>
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
    props: ["stock"],
    components: {
        Head,
        Modal,
        Multiselect,
    },
    data() {
        return {
            table: [
                { title: "Nama Produk", data: "product.design_name" },
                { title: "Jumlah", data: "total" },
                {
                    title: "Tipe",
                    data: "stock_type",
                    render: (row) => {
                        return row.stock_type == 1 ? (
                            <span class={"badge badge-success"}>Baru</span>
                        ) : (
                            <span class={"badge badge-warning"}>Bekas</span>
                        );
                    },
                },
                {
                    title: "Tipe Stok",
                    data: "type",
                    render: (row) => {
                        if (row.type == "IN") {
                            return <span class="text-success">Stock In</span>;
                        } else {
                            return <span class="text-danger">Stock In</span>;
                        }
                    },
                },
                { title: "Toko", data: "store.name" },
                { title: "Supplier", data: "supplier.name" },
                {
                    title: "Status",
                    data: "approval.status",
                    render: (row) => {
                        switch (row.status) {
                            case 0:
                                return (
                                    <span class="badge badge-warning">
                                        Pending
                                    </span>
                                );
                                break;
                            case 1:
                                return (
                                    <span class="badge badge-primary">
                                        Disetujui
                                    </span>
                                );
                                break;
                            case 2:
                                return (
                                    <span class="badge badge-danger">
                                        Ditolak
                                    </span>
                                );
                                break;
                            default:
                                return (
                                    <span class="badge badge-warning">
                                        Pending
                                    </span>
                                );
                                break;
                        }
                    },
                },
            ],
            modalTitle: "",
            modalShow: false,
            selectedIds: [],
            form: {},
            data: this.stock,
            products: [],
            store: [],
            supplier: [
                {
                    id: 2,
                    name: "xx",
                },
            ],
        };
    },
    mounted() {
        this.resetForm();
        this.getProducts();
        this.getDataStore();
        this.getDataSupplier();
    },
    methods: {
        createStock() {
            this.$page.props.errors = null;
            this.resetForm();
            this.modalTitle = "Tambah Data Stok";
            $("#modal-stock-index").modal("show");
        },
        deleteRow(row) {
            this.$confirm(
                "Data yang sudah dihapus tidak dapat dikembalikan!",
                () => this.deleteAction(row),
                false
            );
        },
        deleteAction(row) {
            router.delete(this.route("stock.delete", row.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.$success("Data berhasil dihapus");
                    router.visit(this.$page.url, {
                        only: ["stock"],
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
        async submitForm() {
            try {
                if (this.form.submit) {
                    return;
                }

                this.form.submit = true;
                if (this.form.id == "" || this.form.id == null) {
                    return router.post(this.route("stock.store"), this.form, {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$success("Data berhasil disimpan");
                            router.visit(this.$page.url, {
                                only: ["stock"],
                            });
                            this.form.submit = false;
                            $("#modal-stock-index").modal("hide");
                        },
                        onError: () => {
                            this.form.submit = false;
                            this.$error();
                        },
                    });
                } else {
                    return router.put(
                        this.route("stock.update", this.form.id),
                        this.form,
                        {
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$success("Data berhasil disimpan");
                                router.visit(this.$page.url, {
                                    only: ["stock"],
                                });
                                this.form.submit = false;
                                $("#modal-stock-index").modal("hide");
                            },
                            onError: () => {
                                this.form.submit = false;
                                this.$error();
                            },
                        }
                    );
                }
            } catch (error) {
                this.form.submit = false;
                this.$error("Terjadi kesalahan saat menyimpan data");
            }
        },
        resetForm() {
            this.form = {
                id: null,
                product: null,
                stock_type: 1,
                value: "",
                product_type: 1,
                stock_ref: "",
                submit: false,
            };
        },
        populateForm(data) {
            this.form.id = data.id;
            this.form.product = data.product;
            this.form.product_type = data.stock_type;
            this.form.total = data.total;
            this.form.store = data.store;
            this.form.supplier = data.supplier;
            this.form.store = data.store;
        },
        async getProducts(search = "") {
            try {
                const { data } = await axios.get(
                    this.route("products.get", { search })
                );
                this.products = data;
            } catch (error) {
                console.error("Error loading products", error);
            }
        },
        async getDataStore(val = "") {
            let { data } = await axios.get(
                this.route("store.get", { name: val })
            );
            this.store = data;
        },
        async getDataSupplier(val = "") {
            /* let { data } = await axios.get(
                this.route("supplier.get", { name: val })
            );
            this.supplier = data; */
        },
    },
};
</script>
