<template>
	<table class='ba-table ba-sm ba-lines ba-w-100 center-t'>
        <thead>
            <tr>
                <th>Lost</th>
                <th>#</th>
                <th>Client</th>
                <th>Operator/Department</th>
                <th>Duration</th>
                <th>Start</th>
                <!-- <th>End</th> -->
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, index) in chats_lost" @click="emitopenInfo(item)">
                <td>
                    <div class='ba-flex ba-gp-1 m-r-25'>
                        <div @click="emitOpenChatEvent(item)">
                            <icons-custom class="caret" :active="false" name="view-icon" width="32"></icons-custom>
                        </div>
                        <div @click="emitOpenChatEvent(item)">
                            <icons-custom class="caret" :active="false" name="take-icon" width="22"></icons-custom>
                        </div>
                    </div>
                </td>
                <td>{{item.number}}</td>
                <td class='ba-double'>
                    <div class='ba-title'>{{item.name}}</div>
                    <div class='ba-subtitle'>{{item.name}}</div>
                </td>
                <td class='ba-double'>
                    <!-- <div class='ba-title'>{{item.name}}</div> -->
                    <div class='ba-subtitle'>{{item.department}}</div>
                </td>
                <td :id="'time-elapsed-queue-' + item.chat_id">
                    {{ $calculateWaitingTime( $UTCtoClientTZ(item.created_at, $store.state.tz), "time-elapsed-queue-" + item.chat_id) }}
                </td>
                <td>{{ $UTCtoClientTZ2(item.created_at, $store.state.tz) }}</td>
                <!-- <td>{{ $UTCtoClientTZ2(item.created_at, $store.state.tz) }}</td> -->
            </tr>
        </tbody>
    </table>
</template>

<script>
import iconsCustom from '../../../../util/icons/iconsCustom.vue';
export default {
    components:{
        iconsCustom,
    },
	data(){
		return {
			nome: "THEBESLOKOSOM!",
		}
	},
    props:{
        chats_lost: Array,
    },
	methods: {
        emitopenInfo(item) {
            this.$emit('open-info', item);
        },
        emitOpenChatEvent(item) {
            this.$emit('open-chat', item);
        },
	},
};
</script>

<style scoped>
</style>