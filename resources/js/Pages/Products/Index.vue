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
      <button class="btn btn-primary mb-2" type="button" @click="createProduct">
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
        <h4 class="card-title"><i class="fa fa-box"></i>&nbsp;Data Product</h4>
      </div>
      <Table :columns="table" :rows="data" @update:selectedRows="handleSelectedRows">
        <template #actions="{ row }">
          <button @click="editRow(row)" class="btn btn-warning btn-sm" title="Edit">
            Edit</button
          >&nbsp;
          <button @click="deleteRow(row)" class="btn btn-danger btn-sm" title="Delete">
            Delete
          </button>
        </template>
      </Table>
    </div>
    <Modal :show="modalShow" :title="modalTitle" id="modal-product-index">
      <form ref="formProduct" @submit.prevent="handleSubmit">
        <div class="row">
          <!-- Name -->
          <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Name</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.name }"
              placeholder="Enter Name"
              required
            />
            <span class="text-danger">{{ $page?.props?.errors?.name }}</span>
          </div>

          <!-- Origin -->
          <div class="col-md-6 mb-3">
            <label for="origin" class="form-label">Origin</label>
            <select
              id="origin"
              v-model="form.origin"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.origin }"
            >
              <option value="1">Lokal</option>
              <option value="2">Impor</option>
            </select>
            <span class="text-danger">{{ $page?.props?.errors?.origin }}</span>
          </div>

          <!-- Store ID -->
          <div class="col-md-6 mb-3">
            <label for="toko_id" class="form-label">Store</label>
            <Multiselect
              v-model="form.store"
              :options="store"
              track-by="id"
              label="name"
              @search-change="getDataStore"
              :internal-search="false"
              :class="{ 'is-invalid': $page?.props?.errors?.store }"
            ></Multiselect>
            <span class="text-danger">{{ $page?.props?.errors?.store?.id }}</span>
          </div>

          <!-- Category ID -->
          <div class="col-md-6 mb-3">
            <label for="category_id" class="form-label">Category</label>
            <Multiselect
              v-model="form.category"
              :options="categories"
              track-by="id"
              label="name"
              :internal-search="true"
              :class="{ 'is-invalid': $page?.props?.errors?.category?.id }"
            ></Multiselect>
            <span class="text-danger">{{ $page?.props?.errors?.category_id }}</span>
          </div>

          <!-- Supplier ID -->
          <div class="col-md-6 mb-3">
            <label for="supp_id" class="form-label">Supplier</label>
            <Multiselect
              v-model="form.supplier"
              :options="supplier"
              track-by="id"
              label="name"
              :internal-search="true"
              :class="{ 'is-invalid': $page?.props?.errors?.supplier?.id }"
            ></Multiselect>
            <span class="text-danger">{{ $page?.props?.errors?.supplier?.id }}</span>
          </div>

          <!-- Color -->
          <div class="col-md-6 mb-3">
            <label for="color" class="form-label">Color</label>
            <input
              type="text"
              id="color"
              v-model="form.color"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.color }"
              placeholder="Enter Color"
            />
            <span class="text-danger">{{ $page?.props?.errors?.color }}</span>
          </div>

          <!-- Cost -->
          <div class="col-md-6 mb-3">
            <label for="cost" class="form-label">Cost</label>
            <input
              type="number"
              id="cost"
              v-model="form.cost"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.cost }"
              placeholder="Enter Cost"
            />
            <span class="text-danger">{{ $page?.props?.errors?.cost }}</span>
          </div>

          <!-- Unit Price -->
          <div class="col-md-6 mb-3">
            <label for="unit_price" class="form-label">Unit Price(per meter)</label>
            <input
              type="number"
              id="unit_price"
              v-model="form.unit_price"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.unit_price }"
              placeholder="Enter Unit Price"
            />
            <span class="text-danger">{{ $page?.props?.errors?.unit_price }}</span>
          </div>

          <!-- Design Description -->
          <div class="col-md-6 mb-3">
            <label for="design_desc" class="form-label">Design Description</label>
            <input
              type="text"
              id="design_desc"
              v-model="form.design_desc"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.design_desc }"
              placeholder="Enter Design Description"
            />
            <span class="text-danger">{{ $page?.props?.errors?.design_desc }}</span>
          </div>

          <!-- Pattern Description -->
          <div class="col-md-6 mb-3">
            <label for="pattern_desc" class="form-label">Pattern Description</label>
            <input
              type="text"
              id="pattern_desc"
              v-model="form.pattern_desc"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.pattern_desc }"
              placeholder="Enter Pattern Description"
            />
            <span class="text-danger">{{ $page?.props?.errors?.pattern_desc }}</span>
          </div>

          <!-- Pattern Name -->
          <div class="col-md-6 mb-3">
            <label for="pattern_name" class="form-label">Pattern Name</label>
            <input
              type="text"
              id="pattern_name"
              v-model="form.pattern_name"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.pattern_name }"
              placeholder="Enter Pattern Name"
            />
            <span class="text-danger">{{ $page?.props?.errors?.pattern_name }}</span>
          </div>

          <!-- Design Name -->
          <div class="col-md-6 mb-3">
            <label for="design_name" class="form-label">Design Name</label>
            <input
              type="text"
              id="design_name"
              v-model="form.design_name"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.design_name }"
              placeholder="Enter Design Name"
            />
            <span class="text-danger">{{ $page?.props?.errors?.design_name }}</span>
          </div>

          <!-- Year -->
          <div class="col-md-6 mb-3">
            <label for="year" class="form-label">Year</label>
            <input
              type="number"
              id="year"
              v-model="form.year"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.year }"
              placeholder="Enter Year"
            />
            <span class="text-danger">{{ $page?.props?.errors?.year }}</span>
          </div>

          <!-- Manufacturing Date -->
          <div class="col-md-6 mb-3">
            <label for="mfg_date" class="form-label">Manufacturing Date</label>
            <input
              type="date"
              id="mfg_date"
              v-model="form.mfg_date"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.mfg_date }"
            />
            <span class="text-danger">{{ $page?.props?.errors?.mfg_date }}</span>
          </div>

          <!-- Length -->
          <div class="col-md-6 mb-3">
            <label for="length" class="form-label">Panjang(Meter)</label>
            <input
              type="number"
              id="length"
              v-model="form.length"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.length }"
              placeholder="Enter Length"
            />
            <span class="text-danger">{{ $page?.props?.errors?.length }}</span>
          </div>

          <!-- Width -->
          <div class="col-md-6 mb-3">
            <label for="width" class="form-label">Lebar</label>
            <input
              type="number"
              id="width"
              v-model="form.width"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.width }"
              placeholder="Enter Width"
            />
            <span class="text-danger">{{ $page?.props?.errors?.width }}</span>
          </div>

          <!-- Roll Number -->
          <div class="col-md-6 mb-3">
            <label for="roll_number" class="form-label">Roll Number</label>
            <input
              type="text"
              id="roll_number"
              v-model="form.roll_number"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.roll_number }"
              placeholder="Enter Roll Number"
            />
            <span class="text-danger">{{ $page?.props?.errors?.roll_number }}</span>
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
          title: "Nama Produk",
          data: "name",
        },
        {
          title: "Toko",
          data: "store_name",
        },
        {
          title: "Kategori",
          data: "category_id",
        },
        {
          title: "Supplier",
          data: "supp_id",
        },
        {
          title: "Tipe Produk",
          data: "origin",
          render: (row) => {
            //return <a class={'btn btn-primary'} href='https://xxx.com'>xxx</a>
            return row.origin == 1 ? "Import" : "Local";
          },
          /* render: () => (
                <div>
                <h1>This is JSX content</h1>
                <p>It will be rendered in the child component.</p>
                </div>
            ) */
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
          title: "Harga Per Meter",
          data: "unit_price",
        },
        {
          title: "Deskripsi Desain",
          data: "design_desc",
        },
        {
          title: "Deskripsi Pola",
          data: "pattern_desc",
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
          data: "length",
        },
        {
          title: "Lebar Produk",
          data: "width",
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
      form: {},
      store: [],
    };
  },
  mounted() {
    this.resetForm();
    this.getDataStore();
  },
  methods: {
    createProduct() {
      this.$page.props.errors = null;
      this.resetForm();
      this.modalTitle = "Tambah Data Product";
      $("#modal-product-index").modal("show");
    },
    editRow(row) {
      this.resetForm();
      this.populateForm(row);
      this.getDataStore(row.store?.name);
      this.form.account = row.account;
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
      let { data } = await axios.get(this.route("store.get", { name: val }));
      this.store = data;
    },
    async submitForm() {
      try {
        if (this.form.submit) {
          return;
        }

        this.form.submit = true;
        if (this.form.id == "" || this.form.id == null) {
          return router.post(this.route("products.store"), this.form, {
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
          });
        } else {
          return router.put(this.route("products.update", this.form.id), this.form, {
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
          });
        }
      } catch (e) {
        console.log(e);
        this.form.submit = false;
      }
    },
    resetForm() {
      this.form = {
        id: null,
        product_id: "",
        category: {
          id: null,
          name: "",
        },
        supplier: {
          id: null,
          name: "",
        },
        store: {
          id: null,
          name: "",
        },
        name: "",
        origin: 1,
        color: "",
        cost: "",
        unit_price: "",
        design_desc: "",
        pattern_desc: "",
        pattern_name: "",
        design_name: "",
        year: "",
        mfg_date: "",
        length: 0,
        width: 0,
        roll_number: "",
        submit: false,
        edit: false,
      };
    },
    populateForm(row) {
      console.log(row);
      this.form = {
        id: row.id,
        category: row.category,
        supplier: row.supplier,
        store: row.store,
        name: row.name,
        origin: row.origin,
        color: row.color,
        cost: row.cost,
        unit_price: row.unit_price,
        design_desc: row.design_desc,
        pattern_desc: row.pattern_desc,
        pattern_name: row.pattern_name,
        design_name: row.design_name,
        year: row.year,
        mfg_date: row.mfg_date,
        length: row.length,
        width: row.width,
        roll_number: row.roll_number,
        submit: false,
        edit: true,
      };
    },
  },
};
</script>
