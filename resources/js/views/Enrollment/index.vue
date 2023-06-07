<template>
  <div>
    <div class="container-fluid">
      <div class="container-fluid-body mt-5 mb-5">
        <div
          class="text-white text-center"
        >
          <h1 class="p-3 header-title">
            {{ $t('LANGUAGES.TEXT_ENROLLMENT_PREDICTION_LIST') }}</h1>
        </div>
        <div class="body">
          <FillterList @handleChange="openModalPdf" />
        </div>
      </div>
    </div>
    <EnrollmentPdf :id="idModal" @handleCloseModal="closeModalPdf" />
  </div>
</template>

<script>
import FillterList from './fillter.vue';
import EnrollmentPdf from '../Pdf/EnrollmentPdf.vue';
export default {
  name: 'EnrollmentList',
  components: {
    FillterList,
    EnrollmentPdf,
  },
  data() {
    return {
      idModal: 0,
      isExit: null,
    };
  },
  computed: {

  },
  watch: {

  },
  created() {

  },
  methods: {
    openModalPdf(id) {
      // console.log('Id lay duoc', id);
      this.idModal = id;
    },
    closeModalPdf(id) {
      this.idModal = id;
    },
  },
};
</script>
<style lang="scss" scoped>
.container-fluid {
  width: 90%;
  .container-fluid-body {
    border: 1px solid #cccccc;
    border-top: 0;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;

  }
::v-deep .table thead th {
  border-bottom: 1px solid #888888 !important;
}
}
  ::v-deep .page-item {
    cursor:pointer;
  }
  ::v-deep .Table {
    overflow: inherit;
  }
  .header-title {
  background: #3189bb;
  font-size: 35px;
  border-radius: 20px;
}
.container-fluid-body {
  overflow-x: hidden;
}
::v-deep select:first-child:disabled {
  color: #6f737c;
}
::v-deep option {
  color: #111111;
}
::v-deep option[value=""][disabled] {
  display: none !important;
  color: #6f737c;
}
select:required:invalid { color: #6f737c; }
</style>
