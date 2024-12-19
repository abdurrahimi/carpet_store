<template>
    <Modal :title="modalTitle" id="modal-product-index">
        <form ref="formProduct" @submit.prevent="handleSubmit" v-if="edit">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <img
                        :src="
                            preview ??
                            'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'
                        "
                        onerror="this.onerror=null;this.src='https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'"
                        alt="Image Preview"
                        class="img-fluid"
                        style="max-width: 300px"
                    />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input
                        type="file"
                        id="image"
                        @change="handleImageUpload"
                        class="form-control"
                        accept="image/*"
                        :class="{ 'is-invalid': $page?.props?.errors?.image }"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.image
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="ori_sku" class="form-label">ORI DESIGN SKU</label>
                    <input
                        type="text"
                        id="ori_sku"
                        v-model="form.ori_sku"
                        class="form-control"
                        :class="{ 'is-invalid': $page?.props?.errors?.ori_sku }"
                        placeholder="Enter Ori SKU"
                        required
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.ori_sku
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sku" class="form-label">NEW DESIGN SKU</label>
                    <input
                        type="text"
                        id="sku"
                        v-model="form.sku"
                        class="form-control"
                        :class="{ 'is-invalid': $page?.props?.errors?.sku }"
                        placeholder="Enter New SKU"
                        required
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.sku
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="ori_barcode" class="form-label">ORI BARCODE</label>
                    <input
                        type="text"
                        id="ori_barcode"
                        v-model="form.ori_barcode"
                        class="form-control"
                        :class="{ 'is-invalid': $page?.props?.errors?.ori_barcode }"
                        placeholder="Enter Ori Barcode"
                        required
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.ori_barcode
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input
                        type="text"
                        id="category"
                        v-model="form.category"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.category,
                        }"
                        placeholder="Enter Category"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.category
                    }}</span>
                </div>
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
                    <span class="text-danger">{{
                        $page?.props?.errors?.cost
                    }}</span>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lowest_price" class="form-label"
                        >Lowest Price</label
                    >
                    <input
                        type="number"
                        id="lowest_price"
                        v-model="form.lowest_price"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.lowest_price,
                        }"
                        placeholder="Enter Lowest Price"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.lowest_price
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="higher_price" class="form-label"
                        >Higher Price</label
                    >
                    <input
                        type="number"
                        id="higher_price"
                        v-model="form.higher_price"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.higher_price,
                        }"
                        placeholder="Enter Higher Price"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.higher_price
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="old_reseller_price" class="form-label"
                        >Old Reseller Price</label
                    >
                    <input
                        type="number"
                        id="old_reseller_price"
                        v-model="form.old_reseller_price"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.old_reseller_price,
                        }"
                        placeholder="Enter Old Reseller Price"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.old_reseller_price
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="new_reseller_price" class="form-label"
                        >New Reseller Price</label
                    >
                    <input
                        type="number"
                        id="new_reseller_price"
                        v-model="form.new_reseller_price"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.new_reseller_price,
                        }"
                        placeholder="Enter New Reseller Price"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.new_reseller_price
                    }}</span>
                </div>

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
                    <span class="text-danger">{{
                        $page?.props?.errors?.color
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="pattern" class="form-label">Pattern</label>
                    <input
                        type="text"
                        id="pattern"
                        v-model="form.pattern"
                        class="form-control"
                        :class="{ 'is-invalid': $page?.props?.errors?.pattern }"
                        placeholder="Enter Pattern"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.pattern
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="panjang_per_roll" class="form-label"
                        >Panjang per Roll</label
                    >
                    <input
                        type="number"
                        id="panjang_per_roll"
                        v-model="form.panjang_per_roll"
                        class="form-control"
                        :class="{
                            'is-invalid':
                                $page?.props?.errors?.panjang_per_roll,
                        }"
                        placeholder="Enter Panjang per Roll"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.panjang_per_roll
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="thickness" class="form-label"
                        >Thickness</label
                    >
                    <input
                        type="number"
                        id="thickness"
                        v-model="form.thickness"
                        class="form-control"
                        :class="{
                            'is-invalid':
                                $page?.props?.errors?.thickness,
                        }"
                        placeholder="Enter Panjang per Roll"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.thickness
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="weight" class="form-label"
                        >Weight</label
                    >
                    <input
                        type="number"
                        id="weight"
                        v-model="form.weight"
                        class="form-control"
                        :class="{
                            'is-invalid':
                                $page?.props?.errors?.weight,
                        }"
                        placeholder="Enter Panjang per Roll"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.weight
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="lebar" class="form-label"
                        >Lebar</label
                    >
                    <input
                        type="number"
                        id="lebar"
                        v-model="form.lebar"
                        class="form-control"
                        :class="{
                            'is-invalid':
                                $page?.props?.errors?.lebar,
                        }"
                        placeholder="Enter Panjang per Roll"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.lebar
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipe" class="form-label">Tipe</label>
                    <input
                        type="text"
                        id="tipe"
                        v-model="form.tipe"
                        class="form-control"
                        :class="{ 'is-invalid': $page?.props?.errors?.tipe }"
                        placeholder="Enter Tipe"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.tipe
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

                <div class="col-md-6 mb-3">
                    <label for="backing" class="form-label">Backing</label>
                    <input
                        type="text"
                        id="backing"
                        v-model="form.backing"
                        class="form-control"
                        :class="{ 'is-invalid': $page?.props?.errors?.backing }"
                        placeholder="Enter Backing"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.backing
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="kode_benang" class="form-label"
                        >Kode Benang</label
                    >
                    <input
                        type="text"
                        id="kode_benang"
                        v-model="form.kode_benang"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.kode_benang,
                        }"
                        placeholder="Enter Kode Benang"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.kode_benang
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="reorder_level" class="form-label"
                        >Reorder Level</label
                    >
                    <input
                        type="text"
                        id="reorder_level"
                        v-model="form.reorder_level"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.reorder_level,
                        }"
                        placeholder="Enter Reorder Level"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.reorder_level
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="manufacture_id" class="form-label"
                        >Manufacture ID</label
                    >
                    <input
                        type="text"
                        id="manufacture_id"
                        v-model="form.manufacture_id"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.manufacture_id,
                        }"
                        placeholder="Enter Manufacture ID"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.manufacture_id
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="manufacture_category" class="form-label"
                        >Manufacture Category</label
                    >
                    <input
                        type="text"
                        id="manufacture_category"
                        v-model="form.manufacture_category"
                        class="form-control"
                        :class="{
                            'is-invalid':
                                $page?.props?.errors?.manufacture_category,
                        }"
                        placeholder="Enter Manufacture Category"
                    />
                    <span class="text-danger">{{
                        $page?.props?.errors?.manufacture_category
                    }}</span>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="supplier_id" class="form-label"
                        >Supplier ID</label
                    >
                    <Multiselect
                        v-model="form.supplier"
                        :options="suppliers"
                        track-by="id"
                        label="name"
                        :internal-search="true"
                        :class="{
                            'is-invalid': $page?.props?.errors?.supplier_id,
                        }"
                    >
                    </Multiselect>
                    <span class="text-danger">{{
                        $page?.props?.errors?.supplier_id
                    }}</span>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea
                        id="deskripsi"
                        v-model="form.deskripsi"
                        class="form-control"
                        :class="{
                            'is-invalid': $page?.props?.errors?.deskripsi,
                        }"
                        placeholder="Enter Description"
                    ></textarea>
                    <span class="text-danger">{{
                        $page?.props?.errors?.deskripsi
                    }}</span>
                </div>
            </div>
        </form>
        <div class="row" v-else>
            <div class="col-md-12 mb-3">
                <img
                    :src="preview"
                    onerror="this.src='https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'"
                    alt="Image Preview"
                    class="img-fluid"
                    style="max-width: 300px"
                />
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
                <strong>Category:</strong>
                <p>{{ form.category }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Design Name:</strong>
                <p>{{ form.design_name }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Color:</strong>
                <p>{{ form.color }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Pattern:</strong>
                <p>{{ form.pattern }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Panjang per Roll:</strong>
                <p>{{ form.panjang_per_roll }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Tipe:</strong>
                <p>{{ form.tipe }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Origin:</strong>
                <p>{{ form.origin == 1 ? "Import" : "Lokal" }}</p>
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
                <strong>Manufacture ID:</strong>
                <p>{{ form.manufacture_id }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Manufacture Category:</strong>
                <p>{{ form.manufacture_category }}</p>
            </div>

            <div class="col-md-6 mb-3">
                <strong>Supplier:</strong>
                <p>{{ form.supplier?.name }}</p>
            </div>

            <div class="col-md-12 mb-3">
                <strong>Deskripsi:</strong>
                <p>{{ form.deskripsi }}</p>
            </div>
        </div>
        <template #footer v-if="edit">
            <button
                type="button"
                class="btn btn-primary"
                :class="{ disabled: form.submit }"
                @click="handleSubmit"
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
</template>

<script>
import Modal from "@/Components/Modal.vue";
import Multiselect from "vue-multiselect";
import { router } from "@inertiajs/vue3";
export default {
    components: {
        Modal,
        Multiselect,
    },
    props: ["modalTitle", "editData", "toEdit"],
    watch: {
        editData() {
            this.id = this.editData.id;
            this.preview = this.editData.image != null ? this.editData.image : 'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'
            this.form = {
                sku: this.editData.sku ?? "",
                category: this.editData.category ?? "",
                design_name: this.editData.design_name ?? "",
                color: this.editData.color ?? "",
                pattern: this.editData.pattern ?? "",
                panjang_per_roll: this.editData.panjang_per_roll ?? "",
                tipe: this.editData.tipe ?? "",
                origin: this.editData.origin ?? "",
                backing: this.editData.backing ?? "",
                kode_benang: this.editData.kode_benang ?? "",
                reorder_level: this.editData.reorder_level ?? "",
                manufacture_id: this.editData.manufacture_id ?? "",
                manufacture_category: this.editData.manufacture_category ?? "",
                supplier: this.editData.supplier ?? null,
                deskripsi: this.editData.deskripsi ?? "",
                image: this.editData.image ?? null,
                ori_barcode: this.editData.ori_barcode ?? "",
                thickness: this.editData.thickness ?? 0,
                weight: this.editData.weight ?? 0,
                lebar: this.editData.lebar ?? 0,
            };
        },
        toEdit() {
            this.edit = this.toEdit;
        },
    },
    data() {
        return {
            id: null,
            form: {},
            suppliers: [],
            preview: null,
            submit: false,
            edit: true,
        };
    },
    mounted() {
        this.resetForm();
    },
    methods: {
        handleSubmit() {
            try {
                if (this.submit) {
                    return;
                }

                this.submit = true;
                if (this.id == "" || this.id == null) {
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
                                this.submit = false;
                                $("#modal-product-index").modal("hide");
                            },
                            onError: () => {
                                this.submit = false;
                                this.$error();
                            },
                        }
                    );
                } else {
                    return router.put(
                        this.route("products.update", this.id),
                        this.form,
                        {
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$success("Data berhasil disimpan");
                                router.visit(this.$page.url, {
                                    only: ["products"],
                                });
                                this.submit = false;
                                $("#modal-product-index").modal("hide");
                            },
                            onError: () => {
                                this.submit = false;
                                this.$error();
                            },
                        }
                    );
                }
            } catch (e) {
                console.log(e);
                this.submit = false;
            }
        },
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.preview = e.target.result;
                    this.form.image = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.preview = null;
            }
        },
        resetForm() {
            this.form = {
                sku: this.editData.sku ?? "",
                category: this.editData.category ?? "",
                design_name: this.editData.design_name ?? "",
                color: this.editData.color ?? "",
                pattern: this.editData.pattern ?? "",
                panjang_per_roll: this.editData.panjang_per_roll ?? "",
                tipe: this.editData.tipe ?? "",
                origin: this.editData.origin ?? "",
                backing: this.editData.backing ?? "",
                kode_benang: this.editData.kode_benang ?? "",
                reorder_level: this.editData.reorder_level ?? "",
                manufacture_id: this.editData.manufacture_id ?? "",
                manufacture_category: this.editData.manufacture_category ?? "",
                supplier: this.editData.supplier ?? null,
                deskripsi: this.editData.deskripsi ?? "",
                image: this.editData.image ?? null,
                ori_barcode: this.editData.ori_barcode ?? "",
                thickness: this.editData.thickness ?? 0,
                weight: this.editData.weight ?? 0,
                lebar: this.editData.lebar ?? 0,
            };
        },
        editButtonHandler() {
            this.edit = !this.edit;
        },
        async convertToBase64(file) {
            const reader = new FileReader();

            return new Promise((resolve, reject) => {
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = (error) => reject(error);
            });
        },
    },
};
</script>
