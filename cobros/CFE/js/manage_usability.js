		function sumar()
	    {
	        var valor1=verificar("Mont");
	        var valor2=verificar("Comi");
	        // realizamos la suma de los valores y los ponemos en la casilla del
	        // formulario que contiene el total
	        document.getElementById("Cobr").value=parseFloat(valor1)+parseFloat(valor2);
	    }
		function verificar(id)
	    {
	        var obj=document.getElementById(id);
	        if(obj.value=="")
	            value="0";
	        else
	            value=obj.value;
	         if(validate_importe(value,1))
	        {
	            // marcamos como correcto
	            obj.style.borderColor="#808080";
	            return value;
	        }else{
	            // marcamos como erroneo
	            obj.style.borderColor="#f00";
	            return 0;
	        }
	    }
	    function validate_importe(value,decimal)
	    {
	        if(decimal==undefined)
	            decimal=0;
	 
	        if(decimal==1)
	        {
	            // Permite decimales tanto por . como por ,
	            var patron=new RegExp("^[0-9]+((,|\.)[0-9]{1,2})?$");
	        }else{
	            // Numero entero normal
	            var patron=new RegExp("^([0-9])*$")
	        }
	 
	        if(value && value.search(patron)==0)
	        {
	            return true;
	        }
	        return false;
	    }
		    function saltar(e,id)
		{
			// Obtenemos la tecla pulsada
			(e.keyCode)?k=e.keyCode:k=e.which;
		 	sumar();
		 	CheckEmptyVar("Serv");
			// Si la tecla pulsada es enter (codigo ascii 13)
			if(k==13)
			{
				// Si la variable id contiene "submit" enviamos el formulario
				if(id=="Nomb")
				{
					checkHistory(document.getElementById("Serv").value);
				}
				document.getElementById(id).focus();
				if(id=="Pay")
				{
					document.forms[0].submit();
					alert("submit");
				}
			}
		}
		function CheckEmptyVar(id) {
			var obj=document.getElementById(id);
				if(obj.value=="")
				{
					obj.style.borderColor="#f00";
				}
				else
				{
					obj.style.borderColor="#808080";
				}
		}
		function checkHistory(servicio){
			alert("send   "+servicio);
	                $.ajax({
	                    type: "POST",
	                    url: "http://192.168.6.114/cobros/CFE/php/checkHistory.php",
	                     data: "servicio="+servicio,
	                    success: function(datos){
	                         //alert( "Se guardaron los datos: " + "\n" + datos);
	                         //datos.servicio
	                         //datos.nombre
	                         alert(datos.servico);
	                         if(datos.servico!="")
	                         {
	                         	document.getElementById("Nomb").value=datos.nombre;
	                         }
	                         else{
	                         	document.getElementById("Nomb").value="";
	                         }
	                    }
	                });
	    }
	    