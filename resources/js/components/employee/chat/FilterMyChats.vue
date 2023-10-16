<template>
      <b-form-checkbox
        id="checkbox-1"
        v-model="filter_my_chats"
        name="checkbox-1"
        class="mt-2 fz-18"
      >
        {{ $t("bs-my-chats") }}
      </b-form-checkbox>
</template>
<script>
export default {
    props: {
        filter_my_chats: Number,
    }
}
</script>