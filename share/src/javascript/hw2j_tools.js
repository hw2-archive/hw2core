var Hw2jTools = new function()
{
    this.clearInput = function(id)
    {
        var input = document.getElementsByName(id)[0];
        input.value='';

        var input2 = input.cloneNode(false);
        input2.onchange= input.onchange;
        input.parentNode.replaceChild(input2,input);
    }
    
    this.form = new function() {          
        this.createEditEvents = function(ltid,catid,parPrefix,url)
        {
            // handle layout selectbox
            this.url=url;
            this.parPrefix=parPrefix;
            this.lt_box = document.getElementById(ltid);
            if (this.lt_box) {
                var lt=hw2tools.getParameterByName(parPrefix+"lt");
                if (lt)
                    this.lt_box.options[lt].selected = true;
                this.ltorig=this.lt_box.options[this.lt_box.selectedIndex].value.substring(2);
            }

            // handle category selectbox
            var ltfilter=hw2tools.getParameterByName("ltfilter");
            if (!ltfilter) { // if ltfilter defined, we don't need to check the category layout
                this.cat_box = document.getElementById(catid);
                var parent=hw2tools.getParameterByName("catsel");
                if (parent)
                    this.cat_box.options[parent].selected = true;
                
                this.catorig=this.cat_box.options[this.cat_box.selectedIndex].value;
                
            }
            
            if (!this.lt_box && !this.l)
            
            if (this.lt_box)
               this.lt_box.setAttribute("onChange", "Hw2jTools.form.redirect(\"ltbox\")");
            if (this.cat_box)
                this.cat_box.setAttribute("onChange", "Hw2jTools.form.redirect(\"catbox\")");
        }

        this.redirect= function (elem) {
                if (this.lt_box)
                    var lt=this.lt_box.options[this.lt_box.selectedIndex].value.substring(2);
                var catparent=this.cat_box.options[this.cat_box.selectedIndex].value;
                if ((lt && lt != this.ltorig) || catparent != this.catorig ){
                        var urlString = this.url;
                        urlString += "&catsel="+this.cat_box.selectedIndex;
                        urlString += "&catval="+catparent;
                        if (elem=="ltbox") {
                            urlString += "&"+this.parPrefix+"lt="+this.lt_box.selectedIndex;
                            urlString += "&"+this.parPrefix+"ltName="+lt;
                        }
                        window.location = urlString;
                }     
        }
    }
}