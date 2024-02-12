   //  !!!!READ FIRST!!!! 
 
        //  to use the api you need to use following syntacsis 
    //  keytech_information(id, type, searchBy, Name, returnResult),
    //  where atributes are:
    //  id - material number where we will read informatin,
    //  type - "bom" or "sap" - what we want
    //  searchBy - can be "name" or "description"   - what we know
    //  Name - string with information that we search in 'searchBy'
    //   -can be more than one record
    //   --example - "name1,name2,name3" or "description1,description2,description3"
    //  returnResult - what we are looking for - can be "name" or "description" or "count"(only for bom) or "value"(only for classification)
    //  returnResult is array in JSON format

    document.addEventListener("DOMContentLoaded", function () {

        console.log("DOM has been fully loaded and parsed for test dsu.");
        const formID = "input_25_";    
        const strBom = 'bom';
        const strClassification = 'classification';
        const strDescription = 'description';
        const strName = 'name';
        const strCount = 'count';
        const strValue = 'value';
            
        async function autofill() {
            //splash screen start
            const preloading = document.getElementById("preloading");
            preloading.style.display = "block";
    
            // read Material number
            const materialNumber = formID+19;
            const materialNumberValue = document.getElementById(materialNumber).value; 
            console.log(materialNumberValue);
            
            //takes class form material number fields: M11286  M10841 M11382  M11460 M11461 M13274  
            let first = await keytech_information(materialNumberValue,strClassification,strName,'M10841',strValue);
            console.log(first);       
            document.getElementById(formID+26).value= first['M10841'];
            
            
            
            // read die category
            let DieCategory = document.getElementById(formID+208).value;
            console.log(DieCategory);
    
            switch (DieCategory) {
                case 'Format die':
                    // Basic format number.
                    let first = await keytech_information(materialNumberValue,strClassification,strName,'M11286',strValue);
                    console.log('Basic format number ='+first['M11286']);
                    // Basic format number ID 61
                    document.getElementsByName("input_61[]")[0].value = first['M11286']; // [] is, because the filed is type list
                    document.getElementById(formID+158).value = first['M11382'];
                    break;
                case 'Vario die':
                    let second = await keytech_information(materialNumberValue,strClassification,strName,'M11286,M11460,M11461,M11382',strValue);
                    document.getElementsByName("input_59[]")[0].value= second['M11286']; //Vario format number ID 59
                    document.getElementById(formID + 144).value= second['M11382'];//Vario cut off length
                    document.getElementsByName("input_31")[0].setAttribute("value", second['M11460']); //track
                    document.getElementsByName("input_45")[0].setAttribute("value", second['M11461']); //rows 
                    document.getElementById(formID+31).value = second['M11460']; // Tracks ID 31
                    document.getElementById(formID+45).value = second['M11461']; // Rows ID 45
                    
                    break;   
                case 'Die with tracks':
                    console.log(DieCategory+1);
                    let third = await keytech_information(materialNumberValue,strClassification,strName,'M11460',strValue);
                    document.getElementById(formID+31).value = third['M11460'];
                    break; 
    
                default:
                    console.log(DieCategory+2);
                    alert('Die Category is not filled');
                    break;
            }
    
    
            // Effective area input_id 40
        //     let effectiveProcess = document.getElementById(formID+213).value;
        //     console.log('effective process ID40 ='+effectiveProcess);
        //     if(effectiveProcess == 'Sealing surface'){ 
        //         try{  
        //         let first = await keytech_information(materialNumberValue,strClassification,strName,'M13274',strValue);
        //         let result = first['M13274'];
        //         console.log(first['M13274']);
        //         let result_modified = parseFloat(result?.replace(/,/g, "."));
        //         console.log("Parse 1 " + result_modified);
        //         document.getElementsByName("input_40")[0].setAttribute("value", result_modified);
                
        //         jQuery( "#input_25_40" ).val( parseFloat(result.replace(/,/g, ".")) ).change();
        //         jQuery( "#input_25_185" ).change();
        //         }
        //         catch(error){
        //             console.log(error);
        //         }
        // }

    
    
            preloading.style.display = "none";
            alert('Done');
        }
        /** PACK information autofill function */

    async function autofillPackInformation() {
        //splash screen start
        const preloading = document.getElementById("preloading");
        preloading.style.display = "block";

              // Format sketch info
        try{

            const numberFormatSketch = document.getElementById(formID+'237').value; 
            console.log(numberFormatSketch);
            const names = 'M11576,M11577,M11578,M11579,M11580,M11581';
            let first = await keytech_Format_sketch_information(numberFormatSketch,strName,names,strValue);
            console.log(first);
            document.getElementById(formID+"34").value= first['M11576'].replace(",",".");//FN
            jQuery( formID+"34" ).change();
            document.getElementById(formID+"35").value= first['M11577'].replace(",",".");//FN
            jQuery( formID+"35" ).change();
            document.getElementById(formID+"239").value= first['M11578'].replace(",",".");//FN
            jQuery( formID+"239" ).change();
            document.getElementById(formID+"240").value= first['M11579'].replace(",",".");//FN
            jQuery( "input_25_240" ).change();
            jQuery( "#input_25_240" ).change();
           
            document.getElementById(formID+"236").value= first['M11580'].replace(",",".");//FN
            jQuery( formID+"236" ).change();
            document.getElementById(formID+"235").value= first['M11581'].replace(",",".");//FN
            jQuery( formID+"235" ).change();
            jQuery( "input_25_235" ).change();
            jQuery( "#input_25_235" ).change();
        }catch(error){
            console.log(error);
        }
       
        preloading.style.display = "none";
        alert('Done');
    }
    
        async function buttonConfirm() {
            let text = "This function will get the values from SAP and overwrite existing. Do you really want to do this ?";
                   
            if (confirm(text) == true) {
            console.log( "You pressed OK!");
            await autofill();
    
            } else {
            console.log("You canceled!");
            }
    
            
        }
        async function confirmPack() {
            let text = "This function will get the pack information values from SAP and overwrite existing. Do you really want to do this ?";
                   
            if (confirm(text) == true) {
            console.log( "You pressed OK!");
            await autofillPackInformation();
    
            } else {
            console.log("You canceled!");
            }
    
            
        }
    
        async function keytech_information(id, type, searchBy, Name, returnResult) {
    
            // check if input data is JSON
         
            var communication_file = "/JsonConfigReader/app/jsonexcel/Autofill/keytech_communication_comb_v2.php";
            let result;
    
                try {
                result = await jQuery.ajax({
                
    
                        url: communication_file,
                        type: "POST",
                        data: { "id": id, "type": type, "search_by": searchBy, "names":  Name, 'return': returnResult }
    
                    });
    
               
                    return JSON.parse(result); 
                       
                } catch (error) {
                    console.error(error);
                    return false;
                    
                }
    
    
        }
        //Get info for format sketch from keytech(SAP) clasification 
        async function keytech_Format_sketch_information(id,searchBy, names, returnResult) {
    
            // check if input data is JSON
         
            var communication_file = "/JsonConfigReader/app/jsonexcel/Autofill/keytech_format_sketch.php";
            let result;
    
                try {
                result = await jQuery.ajax({
                
    
                        url: communication_file,
                        type: "POST",
                        data: { "id": id,"search_by": searchBy, "names": names,'return': returnResult }
    
                    });
    
                    return JSON.parse(result); 
                    
                } catch (error) {
                    console.error(error);
                    
                }
    
    
        }
           

        //calls jsonReaderService API with getValueForKey function
        function getValueFromKey(site, creds, keyPath, callback) {
            var xhr = new XMLHttpRequest();
        
            var url = 'jsonReaderService.php?action=getValueForKey&site=' + site + '&creds=' + creds + '&keyPath=' + keyPath;
            
            xhr.open('GET', url, true);
        
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        callback(response);
                    } else {
                        console.error('Error: ' + xhr.status);
                        callback(null);
                    }
                }
            };
        
            xhr.send();
        }
    
        /** run function autofill when button is clicked */
        // check = document.getElementById("field_25_206").addEventListener("click",buttonConfirm);
        // /** Pack information autofill */

        // checkPack = document.getElementById("field_25_287").addEventListener("click",confirmPack);

    
       
        
    
    }); 
    