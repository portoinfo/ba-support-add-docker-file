export const formatStatus = function(status, item, type = 'chat', date = null) {
    var isDark = this.$vuetify.theme.isDark;
    var background = "";
    var color = "";
    var text = "";
    var icon = "";
    var response = "";
    switch (status) {
        case 'OPENED':
            background  = isDark ? "#ffb24433" : "#FEE8C8";
            color = "#FFB244";
            text = this.$t('bs-in-queue')
            icon = 'mdi-clock'
            break;

        case 'MERGED':
            background  = "#D5DDE8";
            color = "#4D5D71";
            text = this.$t('bs-merged')
            icon = 'mdi-merge'
            break;
        
        case 'IN_PROGRESS':
            background  = isDark ? "#0080fc33" : "#DCEEFF";
            color = "#0080FC";
            text = this.$t('bs-in-progress')
            icon = 'mdi-replay'
            break;
        case 'CLOSED':
            if(type == 'ticket'){
                // background  = isDark ? "#ff94f9" : "#ff54f660"; //372121
                // color = "#800080";
                // text = this.$t('bs-answered')
                // icon = 'mdi-check'
                if(date == null){
                    background  = isDark ? "#EAD9FF" : "#EAD9FF";
                    color = "#7E1AFD";
                    text = this.$t('bs-answered')
                    icon = 'mdi-email'
                }else{
                    var dataFornecida = new Date(date);
                    var dataAtual = new Date();
                    var diferencaEmMilissegundos = dataAtual - dataFornecida;
                    var diferencaEmDias = diferencaEmMilissegundos / (1000 * 60 * 60 * 24);
                    if (diferencaEmDias > 5) {
                        background  = isDark ? "#35af2b33" : "#C6ECC3";
                        color = "#35AF2B";
                        text = this.$t('bs-finished')
                        icon = 'mdi-check'  
                    } else {
                        background  = isDark ? "#EAD9FF" : "#EAD9FF";
                        color = "#7E1AFD";
                        text = this.$t('bs-answered')
                        icon = 'mdi-email'
                    }
                }
            }else{
                background  = isDark ? "#35af2b33" : "#C6ECC3";
                color = "#35AF2B";
                text = this.$t('bs-finished')
                icon = 'mdi-check'  
            }
            break;
        case 'RESOLVED':
            background  = isDark ? "#35af2b33" : "#C6ECC3";
            color = "#35AF2B";
            text = this.$t('bs-finished')
            icon = 'mdi-check'
            break;

        case 'CANCELED':
            background  = isDark ? "#ffffff33" : "#E9EDF2";
            color = isDark ? "#FFFFFF" : "#262626";
            text = this.$t('bs-canceled')
            icon = 'mdi-cancel'
            break;

        case 'ROBOT':
            background  = isDark ? "#D5DDE8" : "#D5DDE8";
            color = "#4D5D71";
            text = this.$t('bs-creating')
            icon = 'mdi-circle-edit-outline'
            break;
    }

    switch (item) {
        case 'background':
            response = background;
            break;
        case 'color':
            response = color;
            break;
        case 'text': 
            response = text;
            break;
        case 'icon':
            response = icon;
            break;
    }

    return response;
}