<template>
    <div>

        <Head title="Kasir" />
        <div v-if="$page.props.flash.success" class="alert alert-success" role="alert">
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
            {{ $page.props.flash.error }}
        </div>
        <div class="card shadow mb-5">
            <div class="card-body">
                <form id="form-kasir">
                    <table class="table">
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td colspan="5">
                                    <div class="form-group">
                                        <label for="customer" class="form-label">Customer</label>
                                        <Multiselect v-model="form.customer" :options="customer" track-by="id"
                                            placeholder="Pilih Customer" label="name" @search-change="getCustomers"
                                            @select="addCustomer" :internal-search="false"
                                            :class="{ 'is-invalid': $page?.props?.errors?.customer?.id }"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div class="col-md-12 row">
                                        <div v-for="(item, key) in form.data" class="col-xl-6 col-md-12">
                                            <div class="card mb-3 shadow-lg">
                                                <div class="row card-body">
                                                    <!-- Form Section -->
                                                    <div class="col-md-12">
                                                        <div>
                                                            <!-- Remove Button -->
                                                            <div class="text-right">
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    :class="key == 0 ? 'disabled' : ''"
                                                                    @click="removeProduct(key)">Hapus</button>
                                                            </div>
                                                            <div class="col-md-12 text-center">
                                                                <img :src="item.image ??
                                                                    'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'"
                                                                    class="card-img w-50 text-center"
                                                                    alt="Image Preview" />
                                                            </div>
                                                            <!-- Product Selector -->
                                                            <div class="form-group">
                                                                <label for="product_id"
                                                                    class="form-label">Produk</label>
                                                                <Multiselect v-model="form.data[key].product"
                                                                    :options="products" track-by="id"
                                                                    placeholder="Pilih Produk" label="design_name"
                                                                    @search-change="getProducts"
                                                                    @select="pilihData(key)"
                                                                    @remove="resetFormByKey(key)"
                                                                    :internal-search="false"
                                                                    :class="{ 'is-invalid': $page?.props?.errors?.product?.id }">
                                                                </Multiselect>
                                                                <span class="text-danger">{{
                                                                    $page?.props?.errors?.product?.id }}</span>
                                                            </div>

                                                            <!-- Unit Price -->
                                                            <div class="form-group">
                                                                <label for="unit_price" class="form-label">Unit
                                                                    Price / 6m </label>
                                                                <span v-if="form.data[key].product">
                                                                    ({{ formatMoney(form.data[key].product.lowest_price)
                                                                    }}
                                                                    -
                                                                    {{ formatMoney(form.data[key].product.highest_price)
                                                                    }})
                                                                </span>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Rp</span>
                                                                    </div>
                                                                    <input v-money type="text" class="form-control"
                                                                        :disabled="!form.data[key].product"
                                                                        v-model="form.data[key].unit_price"
                                                                        @keyup="calcDisPrice(key)" />
                                                                </div>
                                                                <span class="text-danger">{{
                                                                    $page?.props?.errors?.product?.id }}</span>
                                                            </div>

                                                            <!-- Variants -->
                                                            <div class="p-2 mt-3 rounded" style="background: #f0f0f0">
                                                                <label class="form-label">Variants</label>
                                                                <button class="btn btn-sm btn-primary mb-2"
                                                                    type="button"
                                                                    @click="tambahVariants(key)">+</button>
                                                                <div class="row">
                                                                    <div class="col-md-3 mb-2"
                                                                        v-for="(variant, key_variant) in form.data[key].variants"
                                                                        :key="key_variant">
                                                                        <div class="border p-2 rounded shadow bg-white">
                                                                            <button
                                                                                class="btn btn-sm btn-danger float-right"
                                                                                :class="{ 'disabled': key_variant == 0 }"
                                                                                @click="removeVariant(key, key_variant)"
                                                                                type="button">x</button>
                                                                            <div class="form-group">
                                                                                <label
                                                                                    class="form-label">Panjang</label>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    v-model="variant.panjang"
                                                                                    :disabled="!form.data[key].product" />
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="form-label">Jumlah</label>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    v-model="variant.jumlah"
                                                                                    :disabled="!form.data[key].product" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Quantity -->
                                                            <div class="form-group">
                                                                <label for="jumlah" class="form-label">Jumlah
                                                                    (Meter)</label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        :disabled="!form.data[key].product"
                                                                        :value="totalPanjang(key)" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Meter</span>
                                                                    </div>
                                                                </div>
                                                                <span class="text-danger">{{
                                                                    $page?.props?.errors?.product?.id }}</span>
                                                            </div>

                                                            <!-- Discount and Total Price -->
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="discount_percentage"
                                                                            class="form-label">Discount (%)</label>
                                                                        <div class="input-group">
                                                                            <input type="number" class="form-control"
                                                                                :disabled="!form.data[key].product"
                                                                                v-model="form.data[key].discount_percentage"
                                                                                @keyup="calculateAll(key)" />
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">%</span>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-danger">{{
                                                                            $page?.props?.errors?.discount_percentage
                                                                        }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="discount_price"
                                                                            class="form-label">Discount (Rp)</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input v-money type="text"
                                                                                class="form-control"
                                                                                :disabled="!form.data[key].product"
                                                                                v-model="form.data[key].discount_price"
                                                                                @keyup="calcDisPerc(key)" />
                                                                        </div>
                                                                        <span class="text-danger">{{
                                                                            $page?.props?.errors?.discount_price
                                                                        }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="total_price"
                                                                            class="form-label">Total
                                                                            Price</label>
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input v-money type="text"
                                                                                class="form-control"
                                                                                :value="totalUnitPrice(key)" disabled />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mb-2 col-md-12" type="button" @click="tambahProduct">
                                        + Tambah Product
                                    </button>
                                </td>
                            </tr>
                            <tr v-for="(add, k) in form.additional" :key="k">
                                <td v-if="k == 0" :rowspan="Object.keys(form.additional).length
                                    " class="align-middle">
                                    <label>Additional Fee</label>
                                </td>
                                <td colspan="2">
                                    <div class="form-group">
                                        <label for="additional_fee" class="form-label">
                                            Additional Service {{ k + 1 }}
                                        </label>
                                        <input type="text" class="form-control" v-model="form.additional[k].name"
                                            :disabled="form.data[0]?.product ? false : true" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="additional_fee" class="form-label">Harga</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input v-money class="form-control" type="text"
                                                v-model="form.additional[k].total"
                                                :disabled="form.data[0]?.product ? false : true" min="0" />
                                        </div>
                                    </div>
                                </td>
                                <td class="nt align-middle">
                                    <button v-if="k != 0" class="btn btn-danger btn-sm" @click="removeAdditional(k)">
                                        x
                                    </button>
                                    <button v-if="k == 0" class="btn btn-primary btn-sm" type="button"
                                        @click="tambahFee">
                                        +
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Discount</td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input v-model="form.discount_percentage" class="form-control" type="number"
                                                @keyup="calcTotalDisPrice"
                                                :disabled="form.data[0]?.product ? false : true" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input v-money v-model="form.discount" class="form-control" type="text"
                                                :disabled="form.data[0]?.product ? false : true"
                                                @keyup="calcTotalDisPerc" />
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Total Transaksi</td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input v-money class="form-control" type="text" :value="totalPrice"
                                                disabled />
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">Kasir</td>
                                <td>
                                    <div class="form-group">
                                        <a class="btn btn-primary float-right" @click="submitForm"
                                            :class="{ disabled: form.submit }">Submit</a>
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
    computed: {
        totalPrice() {
            let total = 0;
            let additional = 0;
            this.form.data?.map((v, k) => {
                total += parseInt(this.totalUnitPrice(k)) ?? 0
            })
            this.form.additional?.map((v, k) => {
                let add = v.total.toString().replace(/\./g, "")
                additional += parseInt(add)
            })

            let disc = this.form.discount ?? 0
            const totalTrans = parseInt(total) + parseInt(additional) - parseInt(disc.toString().replace(/\./g, ""))
            return isNaN(totalTrans) ? 0 : totalTrans
        }
    },
    data() {
        return {
            table: [
                { title: "Tanggal", data: "created_at" },
                { title: "Customer", data: "customer.name" },
                { title: "Jumlah", data: "final_price" },
            ],
            modalTitle: "",
            modalShow: false,
            selectedIds: [],
            form: {
                data: [
                    {
                        product: null,
                        unit_top_price: 0,
                        qty: 6,
                        discount_price: 0,
                        discount_percentage: 0,
                        variants: [
                            {
                                panjang: 6,
                                jumlah: 1,
                            },
                        ],
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
                submit: false,
            },
            products: [],
            variant: null,
            customer: [],
            store: [],
            preview: null,
            variants: [
                {
                    panjang: 0,
                    jumlah: 0,
                },
            ]
        };
    },
    mounted() {
        this.resetForm();
        this.getProducts();
        this.getCustomers();
    },
    methods: {
        async submitForm() {
            try {
                if (this.form.submit) {
                    return;
                }

                this.form.submit = true;

                return router.post(this.route('order.create'), this.form, {
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

            } catch (e) {
                this.form.submit = false;
            }
        },
        totalPriceBeforeDisc() {
            let total = 0;
            let additional = 0;
            this.form.data?.map((v, k) => {
                total += parseInt(this.totalUnitPrice(k)) ?? 0
            })
            this.form.additional?.map((v, k) => {
                let add = v.total.toString().replace(/\./g, "")
                additional += parseInt(add)
            })
            console.log(total)
            return parseInt(total) + parseInt(additional)
        },
        totalUnitPrice(key) {
            let harga = this.form.data[key].unit_price || 0;
            let diskon = this.form.data[key].discount_price || 0;
            let unitPrice = parseInt(harga.toString().replace(/\./g, "")) || 0;
            let qty = parseInt(this.form.data[key].qty) || 0;
            let discountPrice = parseInt(diskon.toString().replace(/\./g, "")) || 0;

            let price = ((unitPrice * qty) / 6) - discountPrice;
            return isNaN(price) ? 0 : price;
        },
        calcDisPrice(key) {
            if (this.form.data[key].discount_percentage == "" || this.form.data[key].discount_percentage == null) {
                this.form.data[key].discount_percentage = 0;
                return
            }
            let unitPrice = this.form.data[key].unit_price.toString().replace(/\./g, "");

            this.form.data[key].discount_price =
                (this.form.data[key].discount_percentage / 100) *
                ((parseInt(unitPrice) * this.form.data[key].qty) / 6);
        },
        calcDisPerc(key) {
            let disc = this.form.data[key].discount_price.toString().replace(/\./g, "")
            let unitPrice = this.form.data[key].unit_price.toString().replace(/\./g, "");
            this.form.data[key].discount_percentage =
                (parseInt(disc) /
                    ((parseInt(unitPrice) * this.form.data[key].qty) / 6)) * 100;
            this.form.data[key].discount_percentage = this.form.data[key].discount_percentage.toFixed(2)
        },
        calcTotalDisPrice() {
            if (this.form.discount_percentage == "" || this.form.discount_percentage == null) {
                this.form.discount_percentage = 0;
                return
            }
            let sumPrice = this.totalPriceBeforeDisc();
            console.log(sumPrice)
            this.form.discount = (this.form.discount_percentage / 100) * parseInt(sumPrice.toString().replace(/\./g, ""));
        },
        calcTotalDisPerc() {
            let disc = this.form.discount.toString().replace(/\./g, "")
            let sumPrice = this.totalPriceBeforeDisc();
            this.form.discount_percentage = (parseInt(disc) / parseInt(sumPrice.toString().replace(/\./g, "")) * 100).toFixed(2);
        },
        formatMoney(value) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
            }).format(value);
        },
        tambahVariants(key) {
            if (!this.form.data[key].product) {
                return
            }

            this.form.data[key].variants.push({
                panjang: 6,
                jumlah: 1,
            });
        },
        removeVariant(key, key_variant) {
            if (key_variant == 0) return
            this.form.data[key].variants.splice(key_variant, 1);
        },
        calculateAll(key) {
            this.calcDisPrice(key)
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
                        qty: 6,
                        discount_price: 0,
                        discount_percentage: 0,
                        variants: [
                            {
                                panjang: 6,
                                jumlah: 1,
                            },
                        ],
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
                submit: false,
            };
        },
        resetFormByKey(key) {
            this.form.data[key] = {
                product: null,
                unit_top_price: 0,
                qty: 6,
                discount_price: 0,
                discount_percentage: 0,
                variants: [
                    {
                        panjang: 6,
                        jumlah: 1,
                    },
                ],
            }
        },
        tambahProduct() {
            this.form.data.push({
                product: null,
                qty: 6,
                unit_top_price: 0,
                discount_price: 0,
                discount_percentage: 0,
                variants: [
                    {
                        panjang: 6,
                        jumlah: 1,
                    },
                ],
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
            this.form.data[key].unit_price = product.lowest_price ?? 0;
            this.form.data[key].image = product.image
        },
        totalPanjang(key) {
            let total = this.form.data[key].variants.reduce((total, variant) => { return total + (variant.panjang * variant.jumlah) }, 0)
            this.form.data[key].qty = total;
            return total;
        },
        getCustomers(search) {
            axios.get(this.route("customer.data", { search: search })).then((res) => {
                this.customer = res.data;
            });
        },
    },
};
</script>
<style scoped>
.form-group {
    margin-bottom: 0px !important;
}
</style>
