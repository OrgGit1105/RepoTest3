<template>
  <div>
    <b-modal id="bv-modal-pdf" hide-footer hide-header size="xl">
      <header class="style-title-modal p-3 text-white">
        <!-- <h4>Model PPF - ID:{{ id }}</h4> -->
        <h4>{{ $t('LANGUAGES.TEXT_PREVIEW_PDF') }}</h4>
      </header>
      <body id="contentt" class="modal-body-content">
        <Result :model-id="id" />
      </body>
      <footer>
        <div class="justify-content-end d-flex p-3">
          <b-button
            class="mt-3 w-25 fs-12 btn btn-accept bg-dx-blue"
            squared
            @click="submitPrint()"
          >{{ $t('LANGUAGES.TEXT_PRINT') }}</b-button>
          <b-button
            class="mt-3 ml-3 w-25 fs-12 btn btn-close"
            squared
            @click="hideModal()"
          >{{ $t('LANGUAGES.TEXT_BUTTON_CLOSE') }}</b-button>
        </div>
      </footer>
    </b-modal>
  </div>
</template>
<script>
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';
import Result from '../Enrollment/result.vue';
export default {
  name: 'EnrollmentPdf',
  components: {
    Result,
  },
  props: { id: { type: Number, default: () => {
    return;
  }, require: true }},
  data() {
    return {
    };
  },
  computed: {
    modalId() {
      return this.id;
    },
  },
  watch: {
    modalId() {
      if (this.id !== 0) {
        // console.log('Id lay duoc', this.id);
        this.openModal();
      }
    },
  },
  created() {
  },
  methods: {
    openModal() {
      this.$bvModal.show('bv-modal-pdf');
    },
    hideModal() {
      this.$emit('handleCloseModal', 0);
      this.$bvModal.hide('bv-modal-pdf');
    },
    submitPrint() {
      html2canvas(document.querySelector('.modal-body-content'), { allowTaint: true }).then(canvas => {
        // console.log('canvas', canvas);
        canvas.getContext('2d');
        // eslint-disable-next-line new-cap
        const pdf = new jsPDF('p', 'mm', [canvas.width, canvas.height]);
        const pdfImage = canvas.toDataURL('image/jpeg', 1.0);
        // console.log('pdfImage', pdfImage);
        pdf.addImage(pdfImage, 'JPEG', 0, 0, canvas.width, canvas.height);
        pdf.save('commande.pdf');
      });
    },
  },
};
</script>
<style lang="scss" scoped>
.btn-close {
  background-color: transparent !important;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
::v-deep .btn-accept {
  background: #0f68b1;
}
.btn-accept:hover {
  box-shadow: 0 5px 11px 0 rgb(0 0 0 / 18%), 0 4px 15px 0 rgb(0 0 0 / 15%);
  background: #0f68b1 !important;
  transition: all 0.2s ease-in-out;

}
.btn-close:hover {
  box-shadow: 0 5px 11px 0 rgb(0 0 0 / 18%), 0 4px 15px 0 rgb(0 0 0 / 15%);
  background-color: transparent !important;
  transition: all 0.2s ease-in-out;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
::v-deep #bv-modal-delete___BV_modal_body_ {
  padding: 0 !important;
}
::v-deep #bv-modal-delete___BV_modal_content_ {
  border: 0 !important;
  border-radius: 0 !important;
}
.style-modal h4 {
  font-weight: 300 !important;
  margin-bottom: 0px !important;
}
#canvas {
  background-color: orange;
}
.style-title-modal {
  background: #0f68b1;
}
.style-title-modal h4 {
  font-size: 18px;
}
.style-modal h2 {
  margin: 25px 0px;
}
.style-modal {
  border-bottom: 1px solid #dee2e6;
}
::v-deep .modal-body {
  padding: 0 !important;
}
.modal-body-content {
   padding: 60px;
}
</style>
