<template>
	<v-dialog :value="true" fullscreen scrollable persistent>
		<v-card color="containerBackground">
			<v-card-title class="pa-0" style="height: 71px;">
                <slot name="header"></slot>
            </v-card-title>
			<v-card-text id="chat-scroll" v-chat-scroll="{always: false, smooth: true}" class="pt-10 px-0">
				<slot name="main"></slot>
			</v-card-text>
			<v-card-actions>
				<v-row no-gutters>
                    <v-col cols="12" style="height: fit-content">
                        <slot name="footer"></slot>
                    </v-col>
                </v-row>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>