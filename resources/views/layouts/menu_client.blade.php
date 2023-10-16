<div class="d-flex flex-column flex-md-row align-items-center p-2 px-md-4 bg-white border-bottom box-shadow">
     <h5 class="my-0 mr-md-auto font-weight-normal"><a href="#" class="" style="color: #A9E2F3;font-size: 20px"><i class="fa fa-list-ul fa-1x" aria-hidden="true"></i></a></h5>

    <nav class="my-2 my-md-0 mr-md-3">
        <a class="p-2 text-primary" href="{{route('home')}}"><b>Home</b></a>
        <a class="p-2 text-primary" href="{{route('chat')}}"><b>Chats</b></a>
        <a class="p-2 text-primary" href="{{route('ticket')}}"><b>Tickets</b></a>
    </nav>

    @if(Auth::user())
	    <a onclick="document.getElementById('id01').style.display='block'"
	    style="width:auto;" style="float: right"  
	    type="button" 
	    href="logout" 
	    class="btn btn-outline-primary">
	    {{Auth::user()->name}}
		</a>
    @else 
        @if(!Auth::user())
            <a onclick="document.getElementById('id01').style.display='block'" 
            style="width:auto;" style="float: right"  
            type="button" 
            href="{{route('logout')}}" 
            class="btn btn-outline-primary">
            	{{session('user')->name}}
        	</a>
        @endif
    @endif
</div>
