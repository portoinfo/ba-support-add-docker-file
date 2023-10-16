<template>
    <button @click="this.activeIncognito" id="incognito">
        <span style="font-size: 18px" class="material-icons-outlined">visibility</span>
    </button>
</template>

<script>
export default {
   props: {
     chat: Object,
     incognito_mode: Boolean,
     incognito_id: String,
     activeIncognito: Function,
   },
   methods:{
     iconSrc(){
       if(this.incognito_mode) {
          return 'images/icons/chat/eye_active.svg';
       } else {
          return 'images/icons/chat/eye.svg';
       }
     }
   }
}
</script>

