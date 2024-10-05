<template>
    <div>
        <Head title="Kasir" />
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
        <div class="card shadow mb-5">
            <div class="card-body">
                <form id="form-kasir">
                    <button
                        class="btn btn-primary btn-sm mb-2 float-right"
                        type="button"
                        @click="tambahProduct"
                    >
                        + Tambah Product
                    </button>
                    <table class="table">
                        <thead></thead>
                        <tbody>
                            <template v-for="(item, key) in form.data">
                                <tr>
                                    <td rowspan="2">
                                        <img
                                            :src="
                                                preview ??
                                                'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'
                                            "
                                            alt="Image Preview"
                                            class="img-fluid"
                                            style="max-width: 300px"
                                        />
                                    </td>
                                    <td class="nt">
                                        <div class="form-group">
                                            <label
                                                for="product_id"
                                                class="form-label"
                                                >Produk</label
                                            >
                                            <Multiselect
                                                v-model="form.data[key].product"
                                                :options="products"
                                                track-by="id"
                                                placeholder="Pilih Produk"
                                                label="name"
                                                @search-change="getProducts"
                                                @select="pilihData(key)"
                                                :internal-search="false"
                                                :class="{
                                                    'is-invalid':
                                                        $page?.props?.errors
                                                            ?.product?.id,
                                                }"
                                            ></Multiselect>
                                            <span class="text-danger">{{
                                                $page?.props?.errors?.product
                                                    ?.id
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="nt">
                                        <div class="form-group">
                                            <label
                                                for="jumlah"
                                                class="form-label"
                                                >Jumlah (Meter)</label
                                            >
                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    min="0"
                                                    v-model="form.data[key].qty"
                                                />
                                                <div class="input-group-append">
                                                    <span
                                                        class="input-group-text"
                                                        >Meter</span
                                                    >
                                                </div>
                                            </div>
                                            <span class="text-danger">{{
                                                $page?.props?.errors?.product
                                                    ?.id
                                            }}</span>
                                        </div>
                                    </td>

                                    <td class="nt">
                                        <div class="form-group">
                                            <label
                                                for="jumlah"
                                                class="form-label"
                                                >Unit Price</label
                                            >
                                            <div class="input-group">
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text"
                                                        >Rp</span
                                                    >
                                                </div>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="
                                                        form.data[key]
                                                            .unit_price
                                                    "
                                                    disabled
                                                />
                                            </div>
                                            <span class="text-danger">{{
                                                $page?.props?.errors?.product
                                                    ?.id
                                            }}</span>
                                        </div>
                                    </td>

                                    <td class="nt align-middle" rowspan="2">
                                        <div class="form-group">
                                            <label
                                                for="jumlah"
                                                class="form-label"
                                                >Total Price</label
                                            >
                                            <div class="input-group">
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text"
                                                        >Rp</span
                                                    >
                                                </div>
                                                {{}}
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    :value="totalUnitPrice(key)"
                                                    disabled
                                                />
                                            </div>
                                            <span class="text-danger">{{
                                                $page?.props?.errors?.product
                                                    ?.id
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="nt align-middle">
                                        <a
                                            v-show="key != 0"
                                            class="btn btn-danger btn-sm"
                                            @click="removeProduct(key)"
                                            >x</a
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="nt">
                                        <div class="form-group">
                                            <label
                                                for="product_id"
                                                class="form-label"
                                                >Variants</label
                                            >
                                            <Multiselect
                                                v-model="form.data[key].product"
                                                :options="products"
                                                track-by="id"
                                                placeholder="Pilih Produk"
                                                label="name"
                                                @search-change="getProducts"
                                                :internal-search="false"
                                                :class="{
                                                    'is-invalid':
                                                        $page?.props?.errors
                                                            ?.product?.id,
                                                }"
                                            ></Multiselect>
                                            <span class="text-danger">{{
                                                $page?.props?.errors?.product
                                                    ?.id
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="nt">
                                        <div class="form-group">
                                            <label
                                                for="jumlah"
                                                class="form-label"
                                                >Discount (Rp)</label
                                            >

                                            <div class="input-group">
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text"
                                                        >Rp</span
                                                    >
                                                </div>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    @keyup="calcDisPerc(key)"
                                                    v-model="
                                                        form.data[key]
                                                            .discount_price
                                                    "
                                                />
                                            </div>
                                            <span class="text-danger">{{
                                                $page?.props?.errors
                                                    ?.discount_price
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="nt">
                                        <div class="form-group">
                                            <label
                                                for="jumlah"
                                                class="form-label"
                                                >Discount (%)</label
                                            >

                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    @keyup="calcDisPrice(key)"
                                                    v-model="
                                                        form.data[key]
                                                            .discount_percentage
                                                    "
                                                />
                                                <div class="input-group-append">
                                                    <span
                                                        class="input-group-text"
                                                        >%</span
                                                    >
                                                </div>
                                            </div>
                                            <span class="text-danger">{{
                                                $page?.props?.errors
                                                    ?.discount_percentage
                                            }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr v-for="(add, k) in form.additional" :key="k">
                                <td
                                    v-if="k == 0"
                                    colspan="2"
                                    :rowspan="
                                        Object.keys(form.additional).length
                                    "
                                    class="align-middle"
                                >
                                    <label>Additional Fee</label>
                                </td>
                                <td colspan="2">
                                    <div class="form-group">
                                        <label
                                            for="additional_fee"
                                            class="form-label"
                                        >
                                            Additional Service {{ k + 1 }}
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="form.additional[k].name"
                                        />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label
                                            for="additional_fee"
                                            class="form-label"
                                            >Harga</label
                                        >
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    >Rp</span
                                                >
                                            </div>
                                            <input
                                                class="form-control"
                                                type="text"
                                                v-model="
                                                    form.additional[k].total
                                                "
                                            />
                                        </div>
                                    </div>
                                </td>
                                <td class="nt align-middle">
                                    <button
                                        v-if="k != 0"
                                        class="btn btn-danger btn-sm"
                                        @click="removeAdditional(k)"
                                    >
                                        x
                                    </button>
                                    <button
                                        v-if="k == 0"
                                        class="btn btn-primary btn-sm"
                                        type="button"
                                        @click="tambahFee"
                                    >
                                        +
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">Discount</td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input
                                                v-model="
                                                    form.discount_percentage
                                                "
                                                class="form-control"
                                                type="number"
                                            />
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                    >%</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    >Rp</span
                                                >
                                            </div>
                                            <input
                                                v-model="form.discount"
                                                class="form-control"
                                                type="number"
                                            />
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4">Total Transaksi</td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    >Rp</span
                                                >
                                            </div>
                                            <input
                                                class="form-control"
                                                type="number"
                                                disabled
                                            />
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4">Kasir</td>
                                <td>
                                    <div class="form-group">
                                        <a class="btn btn-primary float-right"
                                            >Submit</a
                                        >
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fa fa-box"></i>&nbsp;Data Penjualan Hari Ini
                </h4>
            </div>
            <Table :columns="table" :rows="data"> </Table>
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
    props: ["data"],
    components: {
        Head,
        Modal,
        Multiselect,
    },
    data() {
        return {
            table: [
                { title: "Tanggal", data: "created_at" },
                { title: "Toko", data: "store.name" },
                { title: "Jumlah", data: "total" },
            ],
            modalTitle: "",
            modalShow: false,
            selectedIds: [],
            form: {},
            products: [],
            variant: null,
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
    },
    methods: {
        totalUnitPrice(key) {
            return (
                this.form.data[key].unit_price * this.form.data[key].qty -
                this.form.data[key].discount_price
            );
        },
        async getProducts(search = "") {
            try {
                const { data } = await axios.get(
                    this.route("products.get", { name: search })
                );
                this.products = data;
            } catch (error) {
                console.error("Error loading products", error);
            }
        },
        resetForm() {
            this.form = {
                data: [
                    {
                        product: null,
                        unit_top_price: 0,
                        qty: 0,
                        discount_price: 0,
                        discount_percentage: 0,
                    },
                ],
                customer: null,
                discount: 0,
                discount_percentage: 0,
                additional: [
                    {
                        name: "",
                        total: 0,
                    },
                ],
                total: 0,
            };
        },
        tambahProduct() {
            this.form.data.push({
                product: null,
                qty: 0,
                unit_top_price: 0,
                discount_price: 0,
                discount_percentage: 0,
                variant: null,
            });
        },
        tambahFee() {
            this.form.additional.push({
                name: "",
                total: 0,
            });
        },
        removeAdditional(key) {
            if (key == 0) return;
            this.form.additional.splice(key, 1);
        },
        removeProduct(key) {
            if (key == 0) return;
            this.form.data.splice(key, 1);
        },
        addCustomer() {
            console.log("okok");
        },
        pilihData(key) {
            const product = this.form.data[key].product;
            this.form.data[key].unit_price = product.unit_top_price ?? 0;
        },
        calcDisPrice(key) {
            this.form.data[key].discount_price =
                this.form.data[key].discount_percentage *
                this.form.data[key].unit_price;
        },
        calcDisPerc(key) {
            this.form.data[key].discount_percentage =
                (this.form.data[key].discount_price /
                    this.form.data[key].unit_price) *
                100;
        },
    },
};
</script>
<style scoped>
/* .nt {
    border-top: 0px !important;
} */
.form-group {
    margin-bottom: 0px !important;
}
</style>
