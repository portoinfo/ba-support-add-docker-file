<div v-if="sidebarShow">
	<nav id="sidebar">
		<ul class="list-unstyled components">
			<li class="active">
				<a href="{{ route('home')}}">
					<center>
						<i class="fa fa-home fa-2x" aria-hidden="true"></i>
						Home
					</center>
				</a>
			</li>
			<li>
				<a href="chat">
					<center>
						<i class="fa fa-comment fa-2x" aria-hidden="true"></i>
						Chat
					</center>
				</a>
			</li>
			<li>
				<a href="ticket">
					<center>
						<i class="fa fa-envelope fa-2x" aria-hidden="true"></i>
						Ticket
					</center>
				</a>
			</li>
			<li>
				<a href="users">
					<center>	
						<i class="fa fa-users fa-2x" aria-hidden="true"></i>
						Users
					</center>
				</a>
			</li>
			<li>
				<a href="database">
					<center>
						<i class="fa fa-database fa-2x" aria-hidden="true"></i>
						Database 
					</center>
				</a>
			</li>
			<li>
				<a href="chart">
					<center>
						<i class="fa fa-area-chart fa-2x" aria-hidden="true"></i>
						Chart
					</center>	
				</a>
			</li>
		</ul>	
		<br>
		<br>
		<br>
	</nav>
</div>