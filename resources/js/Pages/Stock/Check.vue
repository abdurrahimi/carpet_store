<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from 'vue'
import axios from "axios";
defineOptions({
    layout: AuthenticatedLayout
})

const query = ref('')
const results = ref([])
const showPopup = ref(false)
const form = ref({})

// Fake data
const allProducts = [
    { name: 'Samsung Galaxy S24' },
    { name: 'iPhone 15 Pro Max' },
    { name: 'Xiaomi Redmi Note 12' },
    { name: 'Realmi C55' },
    { name: 'Asus ROG Phone 7' },
]

let timeout
const onSearch = () => {
    clearTimeout(timeout)
    timeout = setTimeout(async () => {
        if (query.value.length >= 1) {
            const res = await axios.get(route("stock.check.data", { query: query.value }));
            results.value = res.data
            showPopup.value = true
        } else {
            showPopup.value = false
            results.value = []
        }
    }, 300)
}

const selectProduct = (product) => {
    query.value = product.design_name
    query.id = product.id
    showPopup.value = false
}

const searchDetail = async () => {
    if (query.value.length >= 1) {
        const data = await axios.get(route("stock.check.detail", { id: query.id }));
        form.value = data.data
        $('#modal-product-index').modal('show');
    }
}

const formatCurrency = (value) => {
    if (!value) return '-';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
}

</script>
<template>
    <div>

        <Head title="Check Stok" />
        <div>
            <label class="form-label">Cari Product</label>
            <div class="row">
                <input type="text" class="form-control mb-2 col-md-6" placeholder="Ketikkan Nama Product"
                    v-model="query" @keyup="onSearch"> &nbsp; <button class="btn btn-primary mb-2" type="button"
                    @click="searchDetail">
                    Detail
                </button>
            </div>
            <div v-if="showPopup && results.length"
                class="translate-middle bg-white border rounded shadow p-3 col-md-12 z-50">
                <div class="border-bottom pb-2 mb-3 fw-bold fs-5">Hasil Pencarian</div>

                <ul class="list-group list-group-flush">
                    <li v-for="(product, index) in results" :key="index" @click="selectProduct(product)"
                        class="list-group-item list-group-item-action" style="cursor: pointer;">
                        {{ product.design_name }} - {{ product.color?.name ?? '' }} - stock: {{ product.stock }}
                    </li>
                </ul>
            </div>

        </div>
        <Modal :title="modalTitle" id="modal-product-index" v-if="form.id">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <img :src="form.image"
                        onerror="this.src='https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'"
                        alt="Image Preview" class="img-fluid" style="max-width: 300px" />
                </div>

                <div class="col-md-6 mb-3">
                    <strong>ORI DESIGN SKU:</strong>
                    <p>{{ form.ori_sku }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>NEW DESIGN SKU:</strong>
                    <p>{{ form.sku }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>ORI BARCODE:</strong>
                    <p>{{ form.ori_barcode }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Category:</strong>
                    <p>{{ form.category?.name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Design Name:</strong>
                    <p>{{ form.design_name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Color:</strong>
                    <p>{{ form.color?.name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Pattern:</strong>
                    <p>{{ form.pattern }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Panjang per Roll:</strong>
                    <p>{{ form.panjang_per_roll }} m</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Thickness:</strong>
                    <p>{{ form.thickness }} mm</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Weight:</strong>
                    <p>{{ form.weight }} kg</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Lebar:</strong>
                    <p>{{ form.lebar }} cm</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Origin:</strong>
                    <p>{{ form.supp_id == 1 ? 'Import' : 'Lokal' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Backing:</strong>
                    <p>{{ form.backing }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Kode Benang:</strong>
                    <p>{{ form.kode_benang }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Reorder Level:</strong>
                    <p>{{ form.reorder_level }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Supplier:</strong>
                    <p>{{ form.supplier?.name || 'N/A' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Description:</strong>
                    <p>{{ form.deskripsi }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <hr>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Cost:</strong>
                    <p>{{ formatCurrency(form.cost) }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Lowest Price:</strong>
                    <p>{{ formatCurrency(form.lowest_price) }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Higher Price:</strong>
                    <p>{{ formatCurrency(form.higher_price) }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Old Reseller Price :</strong>
                    <p>{{ formatCurrency(form.old_reseller_price) }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>New Reseller Price:</strong>
                    <p>{{ formatCurrency(form.new_reseller_price) }}</p>
                </div>
            </div>
        </Modal>
    </div>
</template>