<template>
  <div>
    <Head title="Data Store" />
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
        <h4 class="card-title"><i class="fa fa-box"></i>&nbsp;Data Product</h4>
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
          <!-- Name -->
          <div class="col-md-6 mb-3">
            <div class="form-group">
              <label for="name">Nama Store</label>
              <input
                type="text"
                v-model="form.name"
                class="form-control"
                id="name"
                :class="{ 'is-invalid': $page?.props?.errors?.name }"
                required
              />
              <div v-if="$page?.props?.errors?.name" class="invalid-feedback">
                {{ $page?.props?.errors?.name }}
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="toko_id" class="form-label">Manager / PIC</label>
            <Multiselect
              v-model="form.manager"
              :options="karyawan"
              track-by="id"
              label="name"
              @search-change="getDataKaryawan"
              :internal-search="false"
              :class="{ 'is-invalid': $page?.props?.errors?.manager?.id }"
            ></Multiselect>
            <span class="text-danger">{{ $page?.props?.errors?.manager?.id }}</span>
          </div>

          <!-- Phone -->
          <div class="col-md-6 mb-3">
            <div class="form-group">
              <label for="phone">Nomor Telepon</label>
              <input
                type="text"
                v-model="form.phone"
                class="form-control"
                id="phone"
                :class="{ 'is-invalid': $page?.props?.errors?.phone }"
              />
              <div v-if="$page?.props?.errors?.phone" class="invalid-feedback">
                {{ $page?.props?.errors?.phone }}
              </div>
            </div>
          </div>
          <!-- Store Type -->
          <div class="col-md-6 mb-3">
            <div class="form-group">
              <label for="store_type">Tipe Store</label>
              <select
                v-model="form.store_type"
                class="form-control"
                id="store_type"
                :class="{ 'is-invalid': $page?.props?.errors?.store_type }"
              >
                <option value="1">Cabang</option>
                <option value="2">Gudang</option>
              </select>
              <div
                v-if="$page?.props?.errors?.store_type"
                class="invalid-feedback"
              >
                {{ $page?.props?.errors?.store_type }}
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Address -->
          <div class="col-md-12 mb-3">
            <div class="form-group">
              <label for="address">Alamat</label>
              <textarea
                v-model="form.address"
                class="form-control"
                id="address"
                :class="{ 'is-invalid': $page?.props?.errors?.address }"
              ></textarea>
              <div
                v-if="$page?.props?.errors?.address"
                class="invalid-feedback"
              >
                {{ $page?.props?.errors?.address }}
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
          <span v-else class="bott">Save</span>
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
  props: ["store"],
  components: {
    Head,
    Modal,
    Multiselect,
  },
  data() {
    return {
      table: [
        { title: "Nama Store", data: "name" },
        { title: "Manajer / PIC", data: "manager.name" },
        { title: "Alamat", data: "address" },
        { title: "Nomor Telepon", data: "phone" },
        {
            title: "Tipe Store",
            data: "store_type",
            render: (row) => {
                return row.store_type == 1 ? <span>Cabang</span> : <span>Gudang</span>;
            },
        },
      ],
      modalTitle: "",
      modalShow: false,
      selectedIds: [],
      form: {},
      data: this.store,
      karyawan: []
    };
  },
  mounted() {
    this.resetForm();
    this.getDataKaryawan()
  },
  methods: {
    createProduct() {
      this.$page.props.errors = null;
      this.resetForm();
      this.modalTitle = "Tambah Data Product";
      $("#modal-product-index").modal("show");
    },
    editRow(row) {
      this.getDataKaryawan(row.manager?.name)
      this.resetForm();
      this.populateForm(row);
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
      router.delete(this.route("store.delete", row.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.$success("Data berhasil dihapus");
          router.visit(this.$page.url, {
            only: ["store"],
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
          return router.post(this.route("store.store"), this.form, {
            preserveScroll: true,
            onSuccess: () => {
              this.$success("Data berhasil disimpan");
              router.visit(this.$page.url, {
                only: ["store"],
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
          return router.put(this.route("store.update", this.form.id), this.form, {
            preserveScroll: true,
            onSuccess: () => {
              this.$success("Data berhasil disimpan");
              router.visit(this.$page.url, {
                only: ["store"],
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
        this.form.submit = false;
      }
    },
    resetForm() {
      this.form = {
        id: null,
        name: "",
        address: "",
        phone: "",
        store_type: "1",
        manager: {},
        submit: false,
      };
    },
    populateForm(data) {
      this.form.id = data.id;
      this.form.name = data.name;
      this.form.address = data.address;
      this.form.phone = data.phone;
      this.form.store_type = data.store_type;
      this.form.manager = data.manager;
    },
    async getDataKaryawan(val = "") {
      let { data } = await axios.get(this.route("users.get", { name: val }));
      this.karyawan = data;
    },
  },
};
</script>
