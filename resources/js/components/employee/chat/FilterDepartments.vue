<template>
      <multiselect
        id="departments"
        v-model="company_department"
        track-by="name"
        label="name"
        deselect-label=""
        selectLabel=""
        :selectedLabel="$t('bs-selected')"
        deselectGroupLabel=""
        selectGroupLabel=""
        group-values="options"
        group-label="title"
        :group-select="true"
        :multiple="true"
        :placeholder="$t('bs-filter-by-department')"
        :options="departments"
        :searchable="false"
        :allow-empty="false"
      >
      </multiselect>
</template>
<script>
export default {
    props: {
        company_department: Array,
        departments: Array,
    }
}
</script>
