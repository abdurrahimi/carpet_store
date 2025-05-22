<template>
  <div class="container py-5">
    <div class="stepper d-flex justify-content-between position-relative">
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="step text-center position-relative flex-fill"
      >
        <div
          :class="['circle mx-auto', currentStep >= index ? 'bg-primary text-white' : 'bg-light']"
        >
          {{ index + 1 }}
        </div>
        <div
          :class="['label mt-2', currentStep >= index ? 'text-primary fw-bold' : 'text-muted']"
        >
        <div class="connector" :style="{ background: currentStep >= index ? '#4e73df' : '#dee2e6', width: index == currentStep ? '50% !important' : '100%' }"></div>
        <div class="connector" v-if="currentStep == index" style="width: 50%; right:0px;"></div>
        <span class="con-text">    {{ step }}</span>
        </div>
        <!-- Garis antar lingkaran -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: {
        currentStep: {
            type: Number,
            required: true,
        },
    },
  data() {
    return {
      steps: [
        "Pembayaran",
        "Gudang",
        "Pengiriman",
        "Selesai",
      ],
    };
  },
  methods: {
    nextStep() {
      if (this.currentStep < this.steps.length - 1) this.currentStep++;
    },
    prevStep() {
      if (this.currentStep > 0) this.currentStep--;
    },
  },
};
</script>

<style scoped>
.stepper {
  position: relative;
}
.step {
  position: relative;
}
.circle {
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 50%;
  display: inline-block;
  border: 2px solid #4e73df;
  position: relative;
  z-index: 1;
}
.connector {
  height: 5px;
  position: absolute;
  background-color: #dee2e6;
  z-index: 0;
  top: 25%;
}
</style>
