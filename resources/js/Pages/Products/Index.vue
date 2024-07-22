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
    <div
      v-if="$page.props.flash.success"
      class="alert alert-success"
      role="alert"
    >
      {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash.error" class="alert alert-danger" role="alert">
      {{ $page.props.flash.error }}
    </div>
    <div class="card shadow">
      <div class="card-header">
        <h4 class="card-title">
          <i class="fa fa-user"></i>&nbsp;Data Product
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
      <form @submit.prevent="handleSubmit">
        <div class="row">
          <!-- Product Name -->
          <div class="col-md-6 mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input
              type="text"
              id="product_name"
              v-model="form.product_name"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.product_name }"
              placeholder="Enter Product Name"
              required
            />
            <span class="text-danger">{{
              $page?.props?.errors?.product_name
            }}</span>
          </div>

          <!-- Store ID -->
          <div class="col-md-6 mb-3">
            <label for="toko_id" class="form-label">Store</label>
            <Multiselect
              v-model="form.toko_id"
              :options="store"
              track-by="id"
              label="name"
              @search-change="getDataStore"
              :internal-search="false"
              :class="{ 'is-invalid': $page?.props?.errors?.toko_id }"
            ></Multiselect>
            <span class="text-danger">{{ $page?.props?.errors?.toko_id }}</span>
          </div>

          <!-- Product Category -->
          <div class="col-md-6 mb-3">
            <label for="product_category" class="form-label"
              >Product Category</label
            >
            <select
              id="product_category"
              v-model="form.product_category"
              class="form-control"
              :class="{ 'is-invalid': $page?.props?.errors?.product_category }"
            >
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <span class="text-danger">{{
              $page?.props?.errors?.product_category
            }}</span>
          </div>

          <!-- Product Variants -->
          <div class="col-md-12 mb-3">
            <label class="form-label">Product Variants</label>
            <div
              v-for="(variant, index) in form.variants"
              :key="index"
              class="variant-group"
            >
              <div class="row">
                <div class="col-md-5 mb-3">
                  <label class="form-label">Variant Name</label>
                  <input
                    type="text"
                    v-model="variant.name"
                    class="form-control"
                    placeholder="Enter Variant Name"
                    required
                  />
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Length</label>
                  <input
                    type="number"
                    v-model="variant.length"
                    class="form-control"
                    placeholder="Enter Length"
                    required
                  />
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">Color</label>
                  <input
                    type="text"
                    v-model="variant.color"
                    class="form-control"
                    placeholder="Enter Color"
                    required
                  />
                </div>
                <div class="col-md-1 mb-3 align-baseline">
                  <button
                    type="button"
                    class="btn btn-danger mt-4 btn-sm"
                    @click="removeVariant(index)"
                  >
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-primary" @click="addVariant">
              Add Variant
            </button>
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
          <template v-if="form.submit">
            <span
              class="spinner-border spinner-border-sm"
              role="status"
              aria-hidden="true"
            ></span>
            <span class="bott">Loading</span>
          </template>
          <span v-else class="bott">Save</span>
        </button>
      </template>
    </Modal>
  </div>
</template>
  <script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router } from "@inertiajs/vue3";
import Multiselect from "vue-multiselect";
import axios from "axios";
import "vue-multiselect/dist/vue-multiselect.css";
export default {
  layout: AuthenticatedLayout,
  props: ["products", "categories"],
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
          data: "product_name",
        },
        {
          title: "Nama Varian",
          data: "variant_name",
        },
        {
          title: "Panjang Varian",
          data: "variant_length",
        },
        {
          title: "Warna Varian",
          data: "variant_color",
        },
        {
          title: "Nama Toko",
          data: "store_name",
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
      router.delete(this.route("user.destroy", row.id), {
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
              this.form.submit = false;
              $("#modal-product-index").modal("hide");
              this.$success("Data berhasil disimpan");
              router.visit(this.$page.url, {
                only: ["products"],
              });
            },
            onError: () => {
              this.form.submit = false;
              this.$error();
            },
          });
        } else {
          return router.put(
            this.route("users.update", this.form.id),
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
            }
          );
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.form.submit = false;
      }
    },
    resetForm() {
      this.form = {
        id: "",
        product_name: "",
        toko_id: null,
        product_category: null,
        variants: [
          {
            name: "",
            length: "",
            color: ""
          },
        ],
        submit: false,
      };
    },
    populateForm(data) {},
    addVariant() {
      this.form.variants.push({
        name: "",
        length: "",
        color: "",
      });
    },
    removeVariant(index) {
      this.form.variants.splice(index, 1);
    },
  },
};
</script>
  <style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0;
}
</style>
