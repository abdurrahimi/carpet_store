<template>
  <div>
    <Head title="Data History Penjualan" />
    <div class="text-right">
      <button class="btn btn-primary mb-2" type="button" @click="createStock">
        + Stok Baru
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
        <h4 class="card-title"><i class="fa fa-box"></i>&nbsp;Data History Penjualan</h4>
      </div>
      <Table :columns="table" :rows="data" @update:selectedRows="handleSelectedRows">
        <template #actions="{ row }">
          <button @click="deleteRow(row)" class="btn btn-danger btn-sm" title="Delete">
            Delete</button
          >&nbsp;
        </template>
      </Table>
    </div>
    <Modal :show="modalShow" :title="modalTitle" id="modal-stock-index">
      <form ref="formStock" @submit.prevent="handleSubmit">
        <div class="row"></div>
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
      data: this.data,
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

  },
  methods: {
  },
};
</script>
