document.addEventListener("DOMContentLoaded", function () {

    console.log("DOM has been fully loaded and parsed.");
    const formID = 8;
    const bom = 'bom';
    const classification = 'classification';

    async function autofill() {
        //splash screen start
        const preloading = document.getElementById("preloading");
        preloading.style.display = "block";


        // read Material number
        const materialNumber = 'input_8_19';
        const materialNumberValue = document.getElementById(materialNumber).value; 
        console.log(materialNumberValue);

        /** Normal machine width  SAP attribute: M10841 of "Material number"*/
        let first = await keytech_information(materialNumberValue,classification,'M10841');
        console.log('Normal machine width= '+first);
        document.getElementById("input_"+formID+"_26").value = first;

        /**Format cut off length SAP attribute: M11382 of "Material number" */
        first = await keytech_information(materialNumberValue,classification,'M11382');
        console.log('Format cut off length= '+first);
        document.getElementById("input_"+formID+"_27").value = first;

        /** Effective diaphragm area  #IMPORT: SAP attribute "M13267" * 0,7 of "Material number"*/
        first = await keytech_information(materialNumberValue,classification,'M13267');
        second = first *0.7;
        console.log('Effective diaphragm area= '+first+'*0.7 ='+second);
        document.getElementById("input_"+formID+"_121").value = second;

        /**Effective process area  #IMPORT: SAP Attribute M11800 * M11820 of "Heizplatte" (part) --> go BOM down from "Material number" > "Heizplatte" (assembly) > "Heizplatte" (part) */
        first = await keytech_information(materialNumberValue,bom,'Heizplatte'); //number of Heizplatte assembly
        second = await keytech_information(first,bom,'Heizplatte'); // number of heizplatte part
        third = await keytech_information(second,classification,'M11800'); // take M11800 from heizplatte part
        fourth = await keytech_information(second,classification,'M11820'); // take M11820 from heizplatte part
        fifth = third.replace(',','.') * fourth.replace(',','.');
        document.getElementById("input_"+formID+"_40").value = fifth;
        console.log('Effective process area = heizplatte ASM '+first+' => heizplatte part '+second+' => value M11800 = "'+third+'" * value M11820 "'+fourth+'" = '+fifth);
        

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

    async function keytech_information(id,type,Name) {

        // check if input data is JSON
     
        var communication_file = "/jsonexcel/Autofill/keytech_communication_comb.php";
        let result;

            try {
            result = await jQuery.ajax({
            

                    url: communication_file,
                    type: "POST",
                    data: { "id": id, "type": type, "name":  Name }

                });

           
                return JSON.parse(result); 
                   
            } catch (error) {
                console.error(error);
            }


    }

    async function test2 (){
        let test = await keytech_information(109771999,'bom','Heizplatte');
        test1 = test + 1;
        console.log(test1);
    }

    /** run function autofill when button is clicked */
    check = document.getElementById("field_8_148").addEventListener("click",buttonConfirm);

   
    

}); 
