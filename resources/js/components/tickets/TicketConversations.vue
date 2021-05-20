<template>
	<span>
		<div v-for="conversation in conversations.data">
			<p
			class="overflow-auto p-3 mb-3 mb-md-0 mr-md-3 bg-light"
			v-html="conversation.conversation"
			style="min-height:100px; max-height: 600px;"
			></p>
		</div>
		<pagination :data="paginate_conversations" @pagination-change-page="getResults">
            <span slot="prev-nav">&lt; Previous</span> <span slot="next-nav">Next &gt;</span>
        </pagination>
	</span>
</template>
<script>
	import { mapGetters } from "vuex";
	import pagination from 'laravel-vue-pagination';
	export default {
		name: 'TicketConversations',
		props: ['id'],
		data() {
			return {
				page: 1,
				paginate_conversations: {},
			}
		},
		methods: {
			getResults(page) {
	            this.page = page;
	            this.getTicketConversations();
	        },
			getTicketConversations() {
				this.$Progress.start();
				this.$store
					.dispatch("ticket/getTicketConversations", { id: this.id, page: this.page})
					.then(response => {
					this.$Progress.finish();
					this.paginate_conversations = response.data.data
				})
				.catch(error => {
					this.$Progress.fail();
				});
		    },
		},
		mounted() {
			this.getTicketConversations();
		},
		components: {
			pagination,
		},
		computed: {
			...mapGetters({
				conversations: "ticket/activeTicketConversations"
			})
		},
	}
</script>
