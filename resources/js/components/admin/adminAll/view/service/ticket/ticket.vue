<template>
	<div>
        <div class='ba-flex ba-gp-1'>
            <div class="ba-w-100">
                <div class='ba-card-breadcrumb ba-card'>
                    <nav class='ba-breadcrumb'>
                        <ol class='ba-breadcrumb-ol'>
                            <li class='ba-breadcrumb-item ba-main-item'>
                                <a href='#'>
                                    <span class='ba-icon'><icons-custom name="logo-head-icon" width="22"></icons-custom></span>
                                </a>
                            </li>
                            <li class='ba-breadcrumb-item'>
                                <a href='#'>{{ $t('bs-ticket') }}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
                <br>
                <div class='ba-flex ba-gp-1'>
                    <div>
                        <div class="left-out-wall">
                            <div class="ba-montserrat-700 ba-mg-l-1 caret fz-14 color-r">
                                All Chats (384) 
                                <icons-custom style="padding-left: 20%;" :active="false" name="down-icon" width="12"></icons-custom>
                            </div>
                            <div class='ba-sidebar box-shadow-remove' style="width: 182px;">
                                <ul class='ba-nav'>
                                    <li class='ba-nav-item '>
                                        <input type='checkbox' checked class='ba-nav-item-input-status ' id='ba-toggle-expandable-content-1'>
                                        <label class='ba-nav-item-expandable' style="width: 182px;" for='ba-toggle-expandable-content-1'>
                                            <div class='ba-nav-item-expandable-content grid-block'>
                                                <span class='ba-montserrat-500 fz-14'>Active (1)</span>
                                                <icons-custom style="padding-left: 39%;" :active="false" name="down-icon" width="12"></icons-custom>
                                            </div>
                                            <ul class='ba-nav-subitems'>
                                                <hr class='ba-hr-row'>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>In Queue (0)</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link ba-is-active'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>In Progress (1)</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>Finished (231)</span>
                                                    </a>
                                                </li>
                                                <li class='ba-nav-subitem'>
                                                    <a href='#' class='ba-nav-subitem-link'>
                                                        <span class='ba-mg-l-1 ba-montserrat-500 fz-14'>Lost (152)</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="ba-w-100">
                        <div>
                            <div class='ba-card'>
                                <div class="ba-top">
                                    <span class="ba-title" style="font-size:12px">Chats</span>
                                    <div class="ba-box-items ba-flex ba-gp-1 flex-column flex-md-row">
                                    
                                        <button class='ba-btn ba-sm ba-light'>
                                            <icons-custom :active="false" width="15" name="filter-icon"></icons-custom>
                                            View per page
                                        </button>
                                        <button class='ba-btn ba-sm ba-light'>
                                            <icons-custom :active="false" width="15" name="filter-icon"></icons-custom>
                                            Chat Filter
                                        </button>
                                    </div>
                                </div>
                                <table class='ba-table ba-sm ba-lines ba-w-100 center-t'>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Client</th>
                                            <th>Operator/Department</th>
                                            <th>Duration</th>
                                            <th>Start</th>
                                            <th>End</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="index in 10" :key="index">
                                            <td>
                                                <svg width="33" height="25" viewBox="0 0 33 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.9999 19.5336C15.0566 19.5336 16.7239 17.8663 16.7239 15.8096C16.7239 13.7529 15.0566 12.0856 12.9999 12.0856C10.9432 12.0856 9.27588 13.7529 9.27588 15.8096C9.27588 17.8663 10.9432 19.5336 12.9999 19.5336Z" fill="#0072E1"/>
                                                    <path d="M33 7.81116C33 11.6772 29.866 14.8112 26 14.8112C22.134 14.8112 19 11.6772 19 7.81116C19 3.94516 22.134 0.811157 26 0.811157C29.866 0.811157 33 3.94516 33 7.81116Z" fill="#FB6C76"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0017 7.98985C15.7055 7.62213 14.3591 7.43323 13 7.43323C10.4101 7.43323 7.8665 8.11916 5.62777 9.42125C3.38905 10.7233 1.53512 12.5952 0.25457 14.8463C0.0877075 15.1408 0 15.4736 0 15.8122C0 16.1507 0.0877075 16.4835 0.25457 16.7781C1.53468 19.0294 3.38856 20.9013 5.62743 22.2031C7.8663 23.5049 10.4102 24.1901 13 24.1889C15.5898 24.1901 18.1337 23.5049 20.3726 22.2031C22.6018 20.9069 24.4493 19.0455 25.7289 16.8071C20.943 16.6655 17.0952 12.7877 17.0017 7.98985ZM16.1034 11.1653C15.1848 10.5515 14.1048 10.2238 13 10.2238C11.5185 10.2238 10.0977 10.8124 9.05009 11.8599C8.00251 12.9075 7.41398 14.3284 7.41398 15.8098C7.41398 16.9147 7.7416 17.9947 8.3554 18.9133C8.9692 19.8319 9.84161 20.5479 10.8623 20.9707C11.883 21.3934 13.0062 21.5041 14.0898 21.2885C15.1734 21.073 16.1687 20.541 16.9499 19.7598C17.7311 18.9785 18.2631 17.9832 18.4787 16.8996C18.6942 15.816 18.5836 14.6929 18.1608 13.6722C17.738 12.6515 17.022 11.7791 16.1034 11.1653Z" fill="#0072E1"/>
                                                </svg>
                                                <svg width="21" height="23" viewBox="0 0 21 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.9966 15.157C20.9943 14.9663 20.9924 9.84994 20.9924 9.84994C20.9924 8.31969 19.1466 7.80924 17.9947 8.40279C17.7322 7.05915 15.7523 6.68187 14.5823 7.33181C14.1964 6.1551 12.4134 5.82531 11.2899 6.39511V1.71276C11.2894 0.768274 10.3265 0 9.14248 0C7.95753 0 6.99366 0.768274 6.99366 1.71276V11.681L4.53161 9.9245C3.61288 9.269 2.7742 9.13137 2.23199 9.13137C1.2872 9.13137 0.494597 9.54463 0.16229 10.2098C-0.269615 11.0752 0.172529 12.1195 1.34584 13.0032C1.38773 13.0343 1.46638 13.0863 1.57343 13.1553C2.20221 13.5604 3.87212 14.6365 4.08993 15.9353C4.56745 18.3054 6.38164 19.1349 7.70621 19.741C7.7602 19.7655 7.81279 19.7896 7.86538 19.8138V22.629C7.86538 22.8338 8.07342 23 8.33079 23H18.6863C18.9437 23 19.1517 22.8338 19.1517 22.629V19.813C21.0269 18.1986 21.0148 16.9803 20.9966 15.157Z" fill="#0072E1"/>
                                                </svg>
                                            </td>
                                            <td>900</td>
                                            <td class='ba-double'>
                                                <div class='ba-title'>Marlos</div>
                                                <div class='ba-subtitle'>Fernandes</div>
                                            </td>
                                            <td class='ba-double'>
                                                <div class='ba-title'>Administrador</div>
                                                <div class='ba-subtitle'>Suport BR</div>
                                            </td>
                                            <td>1:18:23</td>
                                            <td>1/4/2023 - 1:18 PM</td>
                                            <td>1/4/2023 - 1:18 PM</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="ba-w-100">
                                    <nav class='ba-pagination ba-md'>
                                        <ul class='ba-list' style="justify-content: center;">
                                            <li class='ba-nav-item'>
                                                <a class='ba-back' href='#'>
                                                    <span class='ba-icon'>
                                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-left' viewBox='0 0 16 16'>
                                                            <path fill-rule='evenodd' d='M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z'/>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-link ba-is-active' href='#'>1</a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-link' href='#'>2</a>
                                            </li>
                                            ...
                                            <li class='ba-nav-item'>
                                                <a class='ba-link' href='#'>9</a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-link' href='#'>10</a>
                                            </li>
                                            <li class='ba-nav-item'>
                                                <a class='ba-next' href='#'>
                                                    <span class='ba-icon'>
                                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-arrow-right' viewBox='0 0 16 16'>
                                                            <path fill-rule='evenodd' d='M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z'/>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-out-wall">
                <div>
                    <p class="ba-montserrat-700" style="color: #2A3C51">
                        Chat Information 
                        <icons-custom class="fr caret" width="17" name="close-icon"></icons-custom>
                    </p>
                    <hr class="ba-hr-row ba-mg-t-1">
                    <p style="color:black"># <span class="color-r-t fz-14">990</span></p>
                    <p class="ba-montserrat-700">Initial Form:</p>
                    <p class="ba-montserrat-500">What is your Builderall account ID?</p>
                    <p class="color-r-t fz-14">
                        <div class='ba-box-search-input'>
                            <input type='search' disabled class='ba-input' placeholder='#867891' />
                        </div>
                    </p>
                    <p class="ba-montserrat-500">What is your doubt?</p>
                    <p class="color-r-t fz-14">
                        <div class='ba-box-search-input'>
                            <input type='search' disabled class='ba-input' placeholder='Test' />
                        </div>
                    </p>
                    <p class="ba-montserrat-700">Chat Created By:</p>
                    <p class="ba-montserrat-700 color-r fz-14">Marlos Fernandes</p>
                    <p class="ba-montserrat-700 color-r fz-14">marlos_gpi@gmail</p>
                    <p class="ba-montserrat-700 color-r fz-14">Suport BR</p>
                    <p class="ba-montserrat-700">Builderall Office Data:</p>
                    <p class="ba-montserrat-700 color-r fz-14">19.07.2023</p>
                    <p class="ba-montserrat-700 color-r fz-14">2:32 PM</p>
                    <p class="ba-montserrat-700 color-r fz-14">Mozilla/5.0 (Windows NT 10.0; 
                        Win64;x64)
                        AppleWebKit/537.36 (KTML, like 
                        Gecko)
                        Chrome/114.0.0.0
                        Safari/537.36
                    </p>
                    <div style="text-align: -webkit-center">
                        <button class='ba-btn ba-md ba-blue ba-w-100 ba-mg-t-1'>
                            View Customer History
                        </button>
                        <button class='ba-btn ba-md ba-red ba-w-100 ba-mg-t-1'>
                            Delete
                        </button>
                        <button class='ba-btn ba-md ba-green ba-w-100 ba-mg-t-1'>
                            Categorize
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <br>
   </div>
</template>

<script>
import iconsCustom from '../../../util/icons/iconsCustom.vue';
export default {
    components:{
        iconsCustom,
    },
	data(){
		return {
			nome: "THEBESLOKOSOM!",
		}
	},
	mounted(){
	},
	methods: {
	},
};
</script>

<style scoped>
    .color-r-t{
        color:#4D5D71CC
    }
    .color-r{
        color:#4D5D71
    }
    .grid-block{
        display: block !important;
    }
    .fz-12{
        font-size: 12px !important;
        color: #2A3C51;
    }
    .fz-14{
        font-size: 14px !important;
    }
    .left-out-wall{
        margin-left: -17px;
        padding: 17px;
        background: white;
        width: 188px;
        border-radius: 0px 15px 15px 0px;
    }
    .right-out-wall{
        margin-right: -16px;
        padding: 16px;
        background: white;
        width: 283px;
        border-radius: 15px 0px 0px 15px;
    }
    .center-t{
        text-align: center;
    }
</style>