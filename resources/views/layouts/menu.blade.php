<div class="d-flex flex-column flex-md-row align-items-center p-2 px-md-4 bg-white border-bottom box-shadow">
    <h5 class="my-0 mr-md-auto font-weight-normal">
        <a href="#" class="" style="color: #A9E2F3;font-size: 20px">
            <i class="fa fa-list-ul fa-1x" aria-hidden="true"></i>
        </a>
        <a class="p-4 text-primary" href="selectCompany"><b>{{session('companyselected.name')}}</b></a>
    </h5>

    @if(auth()->user()->created_by == null)
        <nav class="my-2 my-md-0 mr-md-3">
            <a class="p-2 text-primary" href="{{route('home')}}"><b>Home</b></a>
            <a class="p-2 text-primary" href="company"><b>Company</b></a>
            <a class="p-2 text-primary" href="department"><b>Departament</b></a>
            <a class="p-2 text-primary" href="group"><b>Group</b></a>
            <a class="p-2 text-primary" href="agents"><b>Agents</b></a>
        </nav>
    @endif

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
            	Login
        	</a>
        @endif
    @endif
</div>
