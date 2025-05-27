<script setup lang="jsx">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const role = page.props.auth.role;
let btn, btn2;

switch (role) {
  case 'warehouse':
    btn = "Confirm Available";
    btn2 = "Confirm Not Available";
    break;
  default:
    btn = "Approve";
    btn2 = "Reject";
    break;
}

const status = [
  'Menunggu Persetujuan dari ',
  'Permintaan Disetujui Oleh ',
  'Permintaan Ditolak Oleh '
]
</script>
<template>
  <div>

    <Head title="Data History Penjualan" />
    <div class="text-right">
      <!-- <button class="btn btn-primary mb-2" type="button" @click="createStock">
        + Stok Baru
      </button> -->
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
      <Table :columns="table" :rows="penjualan" @update:selectedRows="handleSelectedRows">
        <template #actions="{ row }">
          <button @click="statusLog(row)" class="btn btn-info btn-sm" title="Status Log">
            <i class="fa fa-history"></i>&nbsp;
            Status Log</button>&nbsp;
          <button @click="detailTransaksi(row)" class="btn btn-warning btn-sm" title="Detail Transaksi">
            <i class="fa fa-eye"></i>&nbsp;
            Detail Transaksi</button>&nbsp;
        </template>
      </Table>
    </div>
    <Modal :show="modalShow" :title="modalTitle" id="modal-detail-index">
      <div class="col-md-12">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Nama Pelanggan</th>
              <td>{{ detail?.customer?.name ?? "" }}</td>
            </tr>
            <tr>
              <th>Tanggal</th>
              <td>{{ detail?.created_at ?? "" }}</td>
            </tr>
            <tr>
              <th>Toko</th>
              <td>{{ detail?.store?.name ?? "" }}</td>
            </tr>
            <tr>
              <th>Jumlah</th>
              <td>{{ formatMoney(detail?.final_price ?? 0) }}</td>
            </tr>
            <tr>
              <th>Metode Pembayaran</th>
              <td>{{ detail?.payment_method?.name ?? "" }}</td>
            </tr>
            <tr>
              <th>Metode Pengiriman</th>
              <td>{{ detail?.shipping_method?.name }}</td>
            </tr>
            <tr>
              <th>Status</th>
              <td>
                <span class="badge" :class="{
                  'badge-secondary': detail?.status === 0,
                  'badge-info': detail?.status === 1,
                  'badge-primary': detail?.status === 2,
                  'badge-success': detail?.status === 3,
                  'badge-warning': detail?.status === 4,
                  'badge-dark': detail?.status === 5,
                  'badge-success': detail?.status === 6,
                  'badge-danger': [99, 100, 101].includes(detail?.status)
                }">
                  {{ detail?.status_text }}
                </span>
              </td>
            </tr>
          </thead>
        </table>
        <table class="table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>Discount</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in detail?.order_details" :key="index">
              <td>{{ item.product_name }}</td>
              <td>{{ item.qty }} meter</td>
              <td>{{ item.discount }}</td>
              <td>{{ formatMoney(item.unit_selling_price) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </Modal>
    <Modal :show="modalShowStatus" :title="modalTitle" id="modal-status-index">
      <form ref="formStatus" @submit.prevent="submitApproval">
        <Step :currentStep="currentStep" />
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in approval.data" :key="index">
              <td><span style="font-size: 10px; font-style: italic;">{{ item.created_at }}</span><br>{{ item.detail }}
                (<i>{{ item.user }}</i>)</td>
            </tr>
          </tbody>
        </table>
      </form>
      <template #footer>
        <button type="button" class="btn btn-primary" :class="{ disabled: approval.submit }"
          @click="submitApproval(true)">
          <div v-if="approval.submit">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="bott">Loading</span>
          </div>
          <span v-else class="bott">{{ btn }}</span>
        </button>
        <button type="button" class="btn btn-danger" :class="{ disabled: approval.submit }"
          @click="rejectApproval(false)">
          <div v-if="approval.submit">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="bott">Loading</span>
          </div>
          <span v-else class="bott">{{ btn2 }}</span>
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
import axios from 'axios';
import Step from '@/Components/Step.vue';

export default {
  layout: AuthenticatedLayout,
  props: ["penjualan"],
  components: {
    Head,
    Modal,
    Multiselect,
  },
  data() {
    return {
      table: [
        { title: "Nama Pelanggan", data: "customer.name" },
        { title: "Tanggal", data: "created_at" },
        { title: "Toko", data: "store.name" },
        { title: "Jumlah", data: "total" },
        { title: "Metode Pembayaran", data: "payment_method.name" },
        { title: "Metode Pengiriman", data: "shipping_method.name" },
        {
          title: "Status",
          data: "status",
          render: (row) => {
            switch (row.status) {
              case 0:
                return <span class="badge badge-secondary">Pending</span>;
              case 1:
                return <span class="badge badge-info">AR Approved</span>;
              case 2:
                return <span class="badge badge-primary">Payment Approved</span>;
              case 3:
                return <span class="badge badge-success">Stock Available</span>;
              case 4:
                return <span class="badge badge-warning">Stock Unavailable</span>;
              case 5:
                return <span class="badge badge-dark">Requested to Supplier</span>;
              case 6:
                return <span class="badge badge-success">Sent to Customer</span>;
              case 99:
                return <span class="badge badge-danger">Rejected</span>;
              case 100:
                return <span class="badge badge-danger">Canceled</span>;
              case 101:
                return <span class="badge badge-danger">Invoiced (Unapproved Payment)</span>;
              default:
                return <span class="badge badge-light">Unknown</span>;
            }
          }
        }
      ],
      btnTextApprove: ['Setujui Pembayaran', 'Stock Tersedia', 'Barang Terkirim'],
      modalTitle: "",
      modalShow: false,
      selectedIds: [],
      form: {},
      data: this.data,
      products: [],
      store: [],
      modalShowStatus: false,
      approval: {
        id: null,
        status: null,
        data: [],
        submit: false,
      },
      supplier: [
        {
          id: 2,
          name: "xx",
        },
      ],
      detail: {},
    };
  },
  computed: {
    currentStep() {
      switch (this.approval.status) {
        case 0:
          return 0;
        case 1:
        case 2:
          return 1;
        case 3:
        case 4:
        case 5:
        case 6:
          return 2;
        case 7:
          return 3;
        default:
          return 0;
      }
    }
  },
  mounted() {

  },
  methods: {
    async submitApproval(isApproved) {
      if (this.approval.submit) {
        return
      }
      this.approval.submit = true;

      try {
        console.log("Submitting approval:");

        axios.post(route('order.approve', this.approval.id), this.approval.formData)
          .then((response) => {
            console.log("success", response.data);

            if (response.data.success === false) {
              this.$error(response.data.message || "Approval gagal");
            } else {
              this.$success("Berhasil mengupdate status");
            }

            this.modalShowStatus = false;
            this.approval.submit = false;
            $("#modal-status-index").modal("hide");
            router.visit(this.$page.url, {
              only: ["penjualan"],
            });
          })
          .catch((error) => {
            console.log("error", error);
            this.approval.submit = false;
            this.$error("Gagal mengupdate status");
          })
          .finally(() => {

            this.approval.submit = false;
          });

      } catch (error) {
        console.error("Error submitting approval:", error);
        this.approval.submit = false;
        this.$error("Gagal mengupdate status");
      }

    },
    async rejectApproval() {
      if (this.approval.submit) {
        return
      }
      this.approval.submit = true;
      try {
        console.log("Submitting approval:");

        axios.post(route('order.reject', this.approval.id), this.approval.formData)
          .then((response) => {
            console.log("success", response.data);

            if (response.data.success === false) {
              this.$error(response.data.message || "Approval gagal");
            } else {
              this.$success("Berhasil mengupdate status");
            }

            this.modalShowStatus = false;
            this.approval.submit = false;
            $("#modal-status-index").modal("hide");
            router.visit(this.$page.url, {
              only: ["penjualan"],
            });
          })
          .catch((error) => {
            console.log("error", error);
            this.approval.submit = false;
            this.$error("Gagal mengupdate status");
          })
          .finally(() => {

            this.approval.submit = false;
          });

      } catch (error) {
        console.error("Error submitting approval:", error);
        this.approval.submit = false;
        this.$error("Gagal mengupdate status");
      }
    },
    async statusLog(row) {
      this.modalTitle = "Status Log";
      this.modalShowStatus = true;
      this.approval.id = row.id;
      this.approval.status = row.status;
      const data = await axios.get(route('order.getStatusHistory', { order_id: row.id }));
      this.approval.data = data.data.data;
      this.approval.formData = {
        id: row.id,
        status: row.status,
      };
      $("#modal-status-index").modal("show");
    },
    async handleSelectedRows(selectedRows) {
      this.selectedIds = selectedRows.map((row) => row.id);
    },
    async handleFile(e) {
      this.form.attachment = e.target.files[0]
    },
    async submitComments(orderId) {
      const formData = new FormData()
      formData.append('description', this.form.description)
      if (form.attachment) {
        formData.append('attachment', this.form.attachment)
      }

      router.post(`/orders/${orderId}/comments`, formData, {
        forceFormData: true, // wajib agar dikirim sebagai multipart/form-data
        onSuccess: () => {
          this.$success('Komentar berhasil dikirim')
          form.reset()
        },
        onError: (errors) => {
          this.$error()
          console.error(errors)
        }
      })
    },
    async detailTransaksi(row) {
      this.modalTitle = "Detail Transaksi";
      this.modalShow = true;
      this.form = row;
      this.modalShowStatus = false;

      try {
        const response = await axios.get(route('order.detail', row.id));
        this.detail = response.data.penjualan;
      } catch (error) {
        console.error("Error fetching order details:", error);
        this.$error("Gagal mengambil detail transaksi");
      }

      $("#modal-detail-index").modal("show");
    },
    formatMoney(value) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
      }).format(isNaN(value) ? 0 : value);
    },
  },
};
</script>
