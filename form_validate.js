var mozevent='dummy';

function validateForm(enqform){

	var pass=true;
	var time_at_addr_pass=false;
	var total_time_at_addr=0;
	var failed_field_count=0;
	var checkbox_status = new Array();
	var highlight = new Array();
	if (document.images){
		for (i=0;i<enqform.length;i++){
			var tempobj=enqform.elements[i];
			if(isDefined(tempobj.name)){
				if (tempobj.name.substring(0,4)=="req_"){
					if (((tempobj.type=="text"||tempobj.type=="textarea"||tempobj.type=="select-one")&&tempobj.value=='')||(tempobj.type.toString().charAt(0)=="s"&&tempobj.selectedIndex==-1)){
						pass=false;
						highlight.push('form_' +  tempobj.name.substring(4 , 150));
						failed_field_count ++;
						//break;
					} else if(tempobj.type == 'checkbox'){
						if(!tempobj.checked){
							pass=false;
							//highlight.push('form_' +  tempobj.name.substring(4 , 150));
							failed_field_count ++;
						}
					} else if(tempobj.type=="radio"){
						var varname = tempobj.name.substring(4 , 150);
						if(!checkbox_status[varname]){
							checkbox_status[varname] = new Array();
							checkbox_status[varname]['boxes'] = new Array();
						}
						checkbox_status[varname]['boxes'].push('form_' + varname + '_options_' + tempobj.value);
						if(!checkbox_status[varname]['status']){
							if(tempobj.checked){
								checkbox_status[varname]['status'] = true;
							} else{
								checkbox_status[varname]['status'] = false;
							}
						}
					}
				}
			}
		}
	}
	//Sort radio button pass status
	for (row in checkbox_status){
		if(!checkbox_status[row]['status']){
			pass=false;
			for(y=0;y<checkbox_status[row]['boxes'].length;y++){
				//highlight.push(checkbox_status[row]['boxes'][y]);
				failed_field_count ++;
			}
		}
	}

	if (pass){
		return true
	}	else{
		alert("Please complete all of the required fields in the form!");
		for(i=0; i<highlight.length; i++){
			highlightField(document.getElementById(highlight[i]));
		}
		return false;
	}

}

function highlightField(jshighlight){
	jshighlight.className = 'formrequiredincomplete';
	jshighlight.onfocus = unHighlightField;
}

function unHighlightField(mozevent){
	if(mozevent){
			var jsunhighlight = mozevent.target;
	}
	else if(event){
			var jsunhighlight = event.srcElement
	}
	if(jsunhighlight){
		jsunhighlight.className = '';
	}
	return true;
}


function isDefined(variable)
{
    return (typeof(variable) == "undefined")?  false: true;
}