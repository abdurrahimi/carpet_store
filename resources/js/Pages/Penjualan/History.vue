<script setup lang="jsx">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const role = page.props.auth?.role[0] || 'guest';

const metodePembayaran = [
  'Cash',
  'AR',
  'Transfer Bank',
  'Kartu Kredit',
  'Lainnya'
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
          <button @click="invoice(row)" class="btn btn-success btn-sm" title="Status Log">
            <i class="fa fa-print"></i>&nbsp;
            Invoice</button>&nbsp;
          <button @click="statusLog(row)" class="btn btn-info btn-sm" title="Status Log">
            <i class="fa fa-history"></i>&nbsp;
            Status Log</button>&nbsp;
          <button @click="detailTransaksi(row)" class="btn btn-warning btn-sm" title="Detail Transaksi">
            <i class="fa fa-eye"></i>&nbsp;
            Detail Transaksi</button>&nbsp;
        </template>
      </Table>
    </div>
    <Modal :show="modalShow" title="Detail Transaksi" id="modal-detail-index">
      <div class="col-md-12">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Nama Pelanggan</th>
              <td>{{ detail?.customer?.name ?? "" }}</td>
            </tr>
            <tr>
              <th>Tanggal</th>
              <td>{{ $timeFormat(detail?.created_at ?? false) }}</td>
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
              <td>{{ metodePembayaran[detail?.payment_method] || "N/A" }}</td>
            </tr>
            <tr>
              <th>Metode Pengiriman</th>
              <td>{{ detail?.shipping_method }}</td>
            </tr>
            <tr>
              <th>Status</th>
              <td>
                <div class="d-flex justify-content-between">
                  <span class="badge" :class="{
                    'badge-secondary': detail?.status === 0,
                    'badge-info': detail?.status === 1 || detail?.status,
                    'badge-primary': detail?.status === 2,
                    'badge-success': detail?.status === 3,
                    'badge-warning': detail?.status === 4,
                    'badge-dark': detail?.status === 5,
                    'badge-success': detail?.status === 6,
                    'badge-danger': [99, 100, 101].includes(detail?.status)
                  }">
                    {{ status[detail?.status?.toString()] ?? "N/A" }}
                  </span>
                  <button @click="statusLog(detail)" class="btn btn-info btn-sm" title="Status Log">
                    <i class="fa fa-history"></i>&nbsp;
                    Status Log</button>
                </div>
              </td>
            </tr>
            <tr>
              <th>Attachment</th>
              <td>
                <ul v-if="detail?.attachments?.length > 0">
                  <li v-for="(item, index) in detail?.attachments" :key="index">
                    <a :href="`/storage/${item.path}`" target="_blank">{{ item.name }}</a>
                  </li>
                </ul>
              </td>
            </tr>
          </thead>
        </table>
        <table class="table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>Harga / 6Meter</th>
              <th>Discount</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in detail?.order_details" :key="index">
              <td>{{ item.product_name }}</td>
              <td>{{ item.qty }} meter</td>
              <td>{{ formatMoney(item.unit_selling_price) }}</td>
              <td>{{ item.discount }}</td>
              <td>{{ formatMoney(item.unit_selling_price) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </Modal>
    <Modal :show="modalShowStatus" title="Log Status Transaksi" id="modal-status-index">
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
              <td><span style="font-size: 10px; font-style: italic;">{{ $timeFormat(item.created_at) }}</span><br>
                {{ item.detail }}
                <ul v-if="item.attachment">
                  <li>
                    <a :href="`/storage/${item.attachment}`" class="link text-success font-italic" style="font-size: 12px;" target="_blank">
                      {{ item.attachment.split('/').pop() }}
                    </a>
                  </li>
                </ul>
                <br>
                - (<i>{{ item.user }}</i>)
              </td>
            </tr>
            <tr>
              <td v-if="lampirkan">
                <form enctype="multipart/form-data" @submit.prevent="submitComments(approval.id)">
                  <textarea v-model="lamiran.description" class="form-control" rows="3"
                    placeholder="Tambahkan catatan..."></textarea>
                  <input type="file" class="form-control mt-2" @change="handleFileLampiran" />
                  <button type="submit" class="btn btn-primary mt-2">Kirim</button>
                </form>
              </td>
            </tr>
          </tbody>
        </table>
        <button class="btn" :class="[lampirkan ? 'btn-warning' : 'btn-info',]" type="button"
          @click="() => lampirkan = !lampirkan">{{ !lampirkan ? 'Tambah Lampiran' : 'Batalkan' }}</button>
      </form>
      <template #footer>
        <button v-if="approval.status == 3 || approval.status > 5" type="button" class="btn btn-warning"
          :class="{ disabled: approval.submit }" @click="PrintSuratJalan(true)">
          <div v-if="approval.submit">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="bott">Loading</span>
          </div>
          <span v-else class="bott">Cetak Surat Jalan</span>
        </button>
        <button v-if="approval.status == 4 || approval.status == 5" type="button" class="btn btn-danger"
          :class="{ disabled: approval.submit }" @click="PrintSuratJalan(true)">
          <div v-if="approval.submit">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="bott">Loading</span>
          </div>
          <span v-else class="bott">Cetak Dokumen Supplier</span>
        </button>
        <button v-if="approval.showApprove" type="button" class="btn btn-primary" :class="{ disabled: approval.submit }"
          @click="submitApproval(true)">
          <div v-if="approval.submit">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="bott">Loading</span>
          </div>
          <span v-else class="bott">{{ btn1Text(approval?.status ?? 0) }}</span>
        </button>
        <button v-if="approval.showReject" type="button" class="btn btn-danger" :class="{ disabled: approval.submit }"
          @click="rejectApproval(false)">
          <div v-if="approval.submit">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="bott">Loading</span>
          </div>
          <span v-else class="bott">{{ btn2Text(approval?.status ?? 0) }}</span>
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
      lampirkan: false,
      lamiran: {
        description: "",
        attachment: null,
      },
      table: [
        { title: "No. Invoice", data: "invoice_no" },
        { title: "Nama Pelanggan", data: "customer.name" },
        {
          title: "Tanggal",
          data: "created_at",
          render: (row) => <span>{this.$timeFormat(row.created_at)}</span>
        },
        { title: "Toko", data: "store.name" },
        { title: "Jumlah", data: "final_price" },
        {
          title: "Metode Pembayaran",
          data: "payment_method.name",
          render: (row) => {
            const metodePembayaran = [
              'Cash',
              'AR',
              'Transfer Bank',
              'Kartu Kredit',
              'Lainnya'
            ]
            return <span>{metodePembayaran[row?.payment_method] || "N/A"}</span>;
          }
        },
        { title: "Metode Pengiriman", data: "shipping_method" },
        {
          title: "Status",
          data: "status",
          render: (row) => {
            switch (parseInt(row.status)) {
              case 0:
                return <span class="badge badge-info">Pending</span>;
              case 1:
                return <span class="badge badge-primary">AR Disetujui</span>;
              case -1:
                return <span class="badge badge-info">AR Sedang Diperiksa</span>;
              case 2:
                return <span class="badge badge-primary">Pembayaran Disetujui</span>;
              case 3:
                return <span class="badge badge-primary">Stock Available Di Warehouse</span>;
              case 4:
                return <span class="badge badge-warning">Stock Tidak Available Di Warehouse</span>;
              case 5:
                return <span class="badge badge-info">Permintaan Barang ke Supplier</span>;
              case 6:
                return <span class="badge badge-info">Pengiriman Barang ke Customer</span>;
              case 7:
                return <span class="badge badge-success">Barang Sudah Diterima Customer</span>;
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
        showApprove: false,
        showReject: false,
        submit: false,
      },
      supplier: [
        {
          id: 2,
          name: "xx",
        },
      ],
      detail: {},
      status: {
        "0": "Menunggu Persetujuan",
        "-1": "AR Sedang Diperiksa",
        "1": "Permintaan AR Disetujui",
        "2": "Pembayaran Disetujui",
        "3": "Stok Tersedia di Warehouse",
        "4": "Stok Tidak Tersedia di Warehouse",
        "5": "Permintaan Barang ke Supplier",
        "6": "Barang Sedang Dikirim ke Pelanggan",
        "7": "Barang Sudah Diterima Pelanggan",
        "99": "Ditolak",
        "100": "Dibatalkan",
        "101": "Invoiced (Pembayaran Tidak Disetujui)",
      }
    };
  },
  computed: {
    currentStep() {
      switch (parseInt(this.approval.status)) {
        case -1:
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
          return 99;
      }
    },
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
      this.modalShowStatus = true;
      this.approval.id = row.id;
      this.approval.status = row.status;
      const data = await axios.get(route('order.getStatusHistory', { order_id: row.id }));
      this.approval.data = data.data.data;
      this.approval.formData = {
        id: row.id,
        status: row.status,
      };
      this.handleShowButton(row.status);
      $("#modal-status-index").modal("show");
    },
    async handleSelectedRows(selectedRows) {
      this.selectedIds = selectedRows.map((row) => row.id);
    },
    async handleFile(e) {
      this.form.attachment = e.target.files[0]
    },
    async detailTransaksi(row) {
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
    btn1Text(status) {
      switch (status.toString()) {
        case "0":
          return "Setujui Permintaan";
        case "-1":
          return "Setujui Permintaan AR";
        case "1":
        case "2":
          return "Tandai Stock Tersedia";
        case "3":
          return "Tandai Sedang Dikirim";
        case "4":
          return "Tandai Proses Permintaan ke Supplier";
        case "5":
        case "6":
          return "Tandai Sudah Diterima";
        default:
          return "Approve";
      }
    },
    btn2Text(status) {
      switch (status.toString()) {
        case "0":
          return "Tolak Permintaan";
        case "-1":
          return "Tolak Permintaan AR";
        case "1":
        case "2":
          return "Stock Tidak Tersedia";
        default:
          return "Reject";
      }
    },
    handleShowButton(status) {
      const role = this.$page.props.auth?.role[0] || 'guest';
      this.approval.showApprove = false;
      this.approval.showReject = false;
      switch (status.toString()) {
        case "0":
          if (role == "finance" || role == "super_admin") {
            this.approval.showApprove = true;
            this.approval.showReject = true;
          }
          return "Setujui Permintaan";
        case "-1":
          if (role == "owner" || role == "super_admin") {
            this.approval.showApprove = true;
            this.approval.showReject = true;
          }
          return "Setujui Permintaan AR";
        case "1":
        case "2":
          if (role == "warehouse" || role == "super_admin") {
            this.approval.showApprove = true;
            this.approval.showReject = true;
          }
          return "Stock Tersedia";
        case "3":
          console.log("role", role);
          console.log("status", status);
          if (role == "admin" || role == "super_admin") {
            console.log("showApprove");
            this.approval.showApprove = true;
          }
          return "Tandai Sedang Dikirim";
        case "4":
          if (role == "admin" || role == "super_admin") {
            this.approval.showApprove = true;
          }
          return "Tandai Sedang Proses Supplier";
        case "5":
        case "6":
          if (role == "admin" || role == "super_admin") {
            this.approval.showApprove = true;
          }
          return "Tandai Sudah Diterima";
        default:
          return "Approve";
      }
    },
    handleFileLampiran(e) {
      if (e.target.files.length > 0) {
        this.lamiran.attachment = e.target.files[0];
      } else {
        this.lamiran.attachment = null;
      }
    },
    submitComments(orderId) {
      const formData = new FormData();
      formData.append('description', this.lamiran.description);
      if (this.lamiran.attachment) {
        formData.append('attachment', this.lamiran.attachment);
      }

      router.post(route('order.addAttachment', orderId), formData, {
        forceFormData: true, // wajib agar dikirim sebagai multipart/form-data
        onSuccess: () => {
          this.$success('Komentar berhasil dikirim');
          this.lamiran.description = '';
          this.lamiran.attachment = null;
          this.statusLog(this.approval);
        },
        onError: (errors) => {
          this.$error('Gagal mengirim komentar');
          console.error(errors);
        }
      });
    },
    invoice(row) {
      window.open(this.route('invoice.download', row.id), '_blank');
    }
  },
};
</script>
