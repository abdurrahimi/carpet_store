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
        <Modal :show="modalShow" :title="modalTitle" id="modal-product-index">
            <form ref="formProduct" @submit.prevent="handleSubmit">
                <div class="row">
                    <!-- Category ID -->
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label"
                            >Category</label
                        >
                        <Multiselect
                            v-model="form.category"
                            :options="categories"
                            track-by="id"
                            label="name"
                            :internal-search="true"
                            :class="{
                                'is-invalid':
                                    $page?.props?.errors?.category?.id,
                            }"
                        ></Multiselect>
                        <span class="text-danger">{{
                            $page?.props?.errors?.category?.id
                        }}</span>
                    </div>

                    <!-- Supplier ID -->
                    <div class="col-md-6 mb-3">
                        <label for="supp_id" class="form-label">Supplier</label>
                        <Multiselect
                            v-model="form.supp"
                            :options="suppliers"
                            track-by="id"
                            label="name"
                            :internal-search="true"
                            :class="{
                                'is-invalid': $page?.props?.errors?.supp?.id,
                            }"
                        ></Multiselect>
                        <span class="text-danger">{{
                            $page?.props?.errors?.supp?.id
                        }}</span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="supp_id" class="form-label">Origin</label>
                        <select class="form-control">
                            <option value="1">Import</option>
                            <option value="2">Lokal</option>
                        </select>
                        <span class="text-danger">{{
                            $page?.props?.errors?.supp?.id
                        }}</span>
                    </div>

                    <!-- Ori Design -->
                    <div class="col-md-6 mb-3">
                        <label for="ori_design" class="form-label"
                            >Ori Design</label
                        >
                        <input
                            type="text"
                            id="ori_design"
                            v-model="form.ori_design"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.ori_design,
                            }"
                            placeholder="Enter Ori Design"
                            required
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.ori_design
                        }}</span>
                    </div>

                    <!-- Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.name,
                            }"
                            placeholder="Enter Name"
                            required
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.name
                        }}</span>
                    </div>

                    <!-- Color -->
                    <div class="col-md-6 mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input
                            type="text"
                            id="color"
                            v-model="form.color"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.color,
                            }"
                            placeholder="Enter Color"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.color
                        }}</span>
                    </div>

                    <!-- Design Name -->
                    <div class="col-md-6 mb-3">
                        <label for="design_name" class="form-label"
                            >Design Name</label
                        >
                        <input
                            type="text"
                            id="design_name"
                            v-model="form.design_name"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.design_name,
                            }"
                            placeholder="Enter Design Name"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.design_name
                        }}</span>
                    </div>

                    <!-- Ori Barcode -->
                    <div class="col-md-6 mb-3">
                        <label for="ori_barcode" class="form-label"
                            >Ori Barcode</label
                        >
                        <input
                            type="text"
                            id="ori_barcode"
                            v-model="form.ori_barcode"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.ori_barcode,
                            }"
                            placeholder="Enter Ori Barcode"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.ori_barcode
                        }}</span>
                    </div>

                    <!-- Pattern -->
                    <div class="col-md-6 mb-3">
                        <label for="pattern" class="form-label">Pattern</label>
                        <input
                            type="text"
                            id="pattern"
                            v-model="form.pattern"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.pattern,
                            }"
                            placeholder="Enter Pattern"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.pattern
                        }}</span>
                    </div>

                    <!-- Width -->
                    <div class="col-md-6 mb-3">
                        <label for="lebar" class="form-label">Width</label>
                        <input
                            type="number"
                            id="lebar"
                            v-model="form.lebar"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.lebar,
                            }"
                            placeholder="Enter Width"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.lebar
                        }}</span>
                    </div>

                    <!-- Length -->
                    <div class="col-md-6 mb-3">
                        <label for="panjang" class="form-label">Length</label>
                        <input
                            type="number"
                            id="panjang"
                            v-model="form.panjang"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.panjang,
                            }"
                            placeholder="Enter Length"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.panjang
                        }}</span>
                    </div>

                    <!-- Code Benang -->
                    <div class="col-md-6 mb-3">
                        <label for="kode_benang" class="form-label"
                            >Code Benang</label
                        >
                        <input
                            type="text"
                            id="kode_benang"
                            v-model="form.kode_benang"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.kode_benang,
                            }"
                            placeholder="Enter Code Benang"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.kode_benang
                        }}</span>
                    </div>

                    <!-- Cost -->
                    <div class="col-md-6 mb-3">
                        <label for="cost" class="form-label">Cost</label>
                        <input
                            type="number"
                            id="cost"
                            v-model="form.cost"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.cost,
                            }"
                            placeholder="Enter Cost"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.cost
                        }}</span>
                    </div>

                    <!-- Unit Bottom Price -->
                    <div class="col-md-6 mb-3">
                        <label for="unit_bottom_price" class="form-label"
                            >Unit Bottom Price</label
                        >
                        <input
                            type="number"
                            id="unit_bottom_price"
                            v-model="form.unit_bottom_price"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    $page?.props?.errors?.unit_bottom_price,
                            }"
                            placeholder="Enter Unit Bottom Price"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.unit_bottom_price
                        }}</span>
                    </div>

                    <!-- Unit Top Price -->
                    <div class="col-md-6 mb-3">
                        <label for="unit_top_price" class="form-label"
                            >Unit Top Price</label
                        >
                        <input
                            type="number"
                            id="unit_top_price"
                            v-model="form.unit_top_price"
                            class="form-control"
                            :class="{
                                'is-invalid':
                                    $page?.props?.errors?.unit_top_price,
                            }"
                            placeholder="Enter Unit Top Price"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.unit_top_price
                        }}</span>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.status,
                            }"
                        >
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="text-danger">{{
                            $page?.props?.errors?.status
                        }}</span>
                    </div>

                    <!-- SKU -->
                    <div class="col-md-6 mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input
                            type="text"
                            id="sku"
                            v-model="form.sku"
                            class="form-control"
                            :class="{ 'is-invalid': $page?.props?.errors?.sku }"
                            placeholder="Enter SKU"
                        />
                        <span class="text-danger">{{
                            $page?.props?.errors?.sku
                        }}</span>
                    </div>

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="desc" class="form-label">Description</label>
                        <textarea
                            id="desc"
                            v-model="form.desc"
                            class="form-control"
                            :class="{
                                'is-invalid': $page?.props?.errors?.desc,
                            }"
                            placeholder="Enter Description"
                        ></textarea>
                        <span class="text-danger">{{
                            $page?.props?.errors?.desc
                        }}</span>
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
                    <span v-else class="bott">Save</span>
                </button>
            </template>
        </Modal>
        <Modal
            :show="modalUploadShow"
            :title="modalTitle"
            id="modal-product-upload"
        >
            <form ref="formUpload" @submit.prevent="submitUpload">
                <input type="file" ref="file" @change="handleFileUpload" />
                <button type="submit">Upload</button>
            </form>
            <template #footer>
                <button
                    type="button"
                    class="btn btn-primary"
                    :class="{ disabled: form.submit }"
                    @click="submitUpload"
                >
                    <div v-if="form.submit">
                        <span
                            class="spinner-border spinner-border-sm"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        <span class="bott">Loading</span>
                    </div>
                    <span v-else class="bott">Save</span>
                </button>
            </template>
        </Modal>
    </div>
</template>
<script lang="jsx">
import { defineComponent } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import Multiselect from "vue-multiselect";
import axios from "axios";
import "vue-multiselect/dist/vue-multiselect.css";
export default {
    layout: AuthenticatedLayout,
    props: ["products", "categories", "supplier"],
    components: {
        Head,
        Modal,
        Multiselect,
    },
    data() {
        return {
            table: [
                {
                    title: "SKU",
                    data: "sku",
                },
                {
                    title: "Nama Produk",
                    data: "design_name",
                },
                {
                    title: "Ori Design",
                    data: "ori_design",
                },
                {
                    title: "Supplier",
                    data: "supp_id",
                },
                {
                    title: "Tipe Produk",
                    data: "origin",
                    render: (row) => {
                        return row.origin == 1 ? (
                            <span>Import</span>
                        ) : (
                            <span>Local</span>
                        );
                    },
                },
                {
                    title: "Warna Produk",
                    data: "color",
                },
                {
                    title: "Harga Modal",
                    data: "cost",
                },
                {
                    title: "Harga Bawah",
                    data: "unit_bottom_price",
                }, // Ganti unit_price ke unit_bottom_price
                {
                    title: "Deskripsi Desain",
                    data: "desc", // Ganti design_desc ke desc
                },
                {
                    title: "Deskripsi Pola",
                    data: "pattern", // Ganti pattern_desc ke pattern
                },
                {
                    title: "Nama Pola",
                    data: "pattern_name",
                },
                {
                    title: "Nama Desain",
                    data: "design_name",
                },
                {
                    title: "Tahun",
                    data: "year",
                },
                {
                    title: "Tanggal Produksi",
                    data: "mfg_date",
                },
                {
                    title: "Panjang Produk",
                    data: "panjang", // Ganti length ke panjang
                },
                {
                    title: "Lebar Produk",
                    data: "lebar", // Ganti width ke lebar
                },
                {
                    title: "Dibuat Pada",
                    data: "created_at",
                },
                {
                    title: "Dibuat Oleh",
                    data: "created_by",
                },
                {
                    title: "Diperbarui Pada",
                    data: "updated_at",
                },
                {
                    title: "Diperbarui Oleh",
                    data: "updated_by",
                },
                {
                    title: "Nomor Roll",
                    data: "roll_number",
                },
            ],

            data: this.products,
            modalTitle: "",
            modalShow: false,
            selectedIds: [],
            suppliers: [],
            form: {},
            store: [],
            modalUploadShow: false,
            file: null,
        };
    },
    mounted() {
        this.resetForm();
        this.getDataStore();
    },
    methods: {
        handleFileUpload(event) {
            this.file = event.target.files[0];
        },
        submitUpload() {
            const formData = new FormData();
            formData.append("file", this.file);
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
        },
        createProduct() {
            this.$page.props.errors = null;
            this.resetForm();
            this.modalTitle = "Tambah Data Product";
            $("#modal-product-index").modal("show");
        },
        uploadProduct() {
            this.$page.props.errors = null;
            this.modalTitle = "Tambah Data Product";
            $("#modal-product-upload").modal("show");
        },
        editRow(row) {
            this.resetForm();
            this.populateForm(row);
            this.getDataStore(row.store?.name);
            this.form.account = row.account;
            this.form.id = row.id;
            this.modalTitle = "Ubah Data Karyawan";
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
        },
        handleSelectedRows(selectedRows) {
            this.selectedIds = selectedRows;
        },
        async getDataStore(val = "") {
            let { data } = await axios.get(
                this.route("store.get", { name: val })
            );
            this.store = data;
        },
        async submitForm() {
            try {
                if (this.form.submit) {
                    return;
                }

                this.form.submit = true;
                if (this.form.id == "" || this.form.id == null) {
                    return router.post(
                        this.route("products.store"),
                        this.form,
                        {
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$success("Data berhasil disimpan");
                                router.visit(this.$page.url, {
                                    only: ["products"],
                                });
                                this.form.submit = false;
                                $("#modal-product-index").modal("hide");
                            },
                            onError: () => {
                                this.form.submit = false;
                                this.$error();
                            },
                        }
                    );
                } else {
                    return router.put(
                        this.route("products.update", this.form.id),
                        this.form,
                        {
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$success("Data berhasil disimpan");
                                router.visit(this.$page.url, {
                                    only: ["products"],
                                });
                                this.form.submit = false;
                                $("#modal-product-index").modal("hide");
                            },
                            onError: () => {
                                this.form.submit = false;
                                this.$error();
                            },
                        }
                    );
                }
            } catch (e) {
                console.log(e);
                this.form.submit = false;
            }
        },
        resetForm() {
            this.form = {
                id: null,
                category: {
                    id: null,
                    name: "",
                },
                supplier: {
                    id: null,
                    name: "",
                },
                ori_design: "",
                name: "",
                color: "",
                design_name: "",
                ori_barcode: "",
                pattern: "",
                lebar: 0,
                panjang: 0,
                kode_benang: "",
                cost: 0,
                unit_bottom_price: 0,
                unit_top_price: 0,
                status: 1,
                sku: "",
                desc: "",
                origin: 1,
                submit: false,
                edit: false,
            };
        },
        populateForm(row) {
            this.form = {
                id: row.id,
                category: row.category,
                supplier: row.supplier,
                ori_design: row.ori_design,
                name: row.name,
                color: row.color,
                design_name: row.design_name,
                ori_barcode: row.ori_barcode,
                pattern: row.pattern,
                lebar: row.lebar,
                panjang: row.panjang,
                kode_benang: row.kode_benang,
                cost: row.cost,
                unit_bottom_price: row.unit_bottom_price,
                unit_top_price: row.unit_top_price,
                status: row.status,
                sku: row.sku,
                desc: row.desc,
                origin: row.origin,
                submit: false,
                edit: true,
            };
        },
    },
};
</script>
