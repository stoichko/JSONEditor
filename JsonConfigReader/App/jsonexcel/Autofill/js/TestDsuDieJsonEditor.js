//  !!!!READ FIRST!!!!

//  to use the api you need to use following syntacsis
//  keytech_information(id, type, searchBy, Name, returnResult),
//  where atributes are:
//  id - material number where we will read information,
//  type - "bom" or "sap" - what we want
//  searchBy - can be "name" or "description"   - what we know
//  Name - string with information that we search in 'searchBy'
//   -can be more than one record
//   --example - "name1,name2,name3" or "description1,description2,description3"
//  returnResult - what we are looking for - can be "name" or "description" or "count"(only for bom) or "value"(only for classification)
//  returnResult is array in JSON format

document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM has been fully loaded and parsed for test dsu.");
  const formID = "input_12_";
  const strBom = "bom";
  const strClassification = "classification";
  const strDescription = "description";
  const strName = "name";
  const strCount = "count";
  const strValue = "value";

  async function autofill() {
    //TODO : We dont have 'preloding in html that is why we have some errors'
    // const preloading = document.getElementById("preloading");
    // preloading.style.display = "block";
    // read Material number
    const materialNumber = formID + 19;
    const materialNumberValue = document.getElementById(materialNumber).value;
    console.log(materialNumberValue);

    //takes class form material number fields: M11286  M10841 M11382 M11286 M11460 M11461 M13267
    try {
      let first = await keytech_information(
        materialNumberValue,
        strClassification,
        strName,
        "M10841",
        strValue
      );
      console.log(first);
      document.getElementById(formID + 26).value = first["M10841"];
      jQuery(formID + 26).change();
    } catch (error) {
      console.log(error);
    }

    // read die category
    let DieCategory = document.getElementById(formID + 208).value;
    console.log(DieCategory);

    switch (DieCategory) {
      case "Format die":
        try {
          // Basic format number.
          let first = await keytech_information(
            materialNumberValue,
            strClassification,
            strName,
            "M11286,M11382",
            strValue
          );
          console.log("Basic format number =" + first["M11286"]);
          // Basic format number ID 61
          document.getElementsByName("input_61[]")[0].value = first["M11286"]; // [] is, because the filed is type list
          document.getElementById(formID + 158).value = first["M11382"]; // 158 Format cut off length (auto)
          jQuery(formID + 158).change();
        } catch (error) {
          console.log(error);
        }
        break;
      case "Vario die":
        try {
          let second = await keytech_information(
            materialNumberValue,
            strClassification,
            strName,
            "M11286,M11460,M11461,M11382",
            strValue
          );
          document
            .getElementsByName("input_31")[0]
            .setAttribute("value", second["M11460"]); //track
          document
            .getElementsByName("input_45")[0]
            .setAttribute("value", second["M11461"]); //rows
          document.getElementsByName("input_59[]")[0].value = second["M11286"]; //Vario format number ID 59
          document.getElementById(formID + 31).value = second["M11460"]; // Tracks ID 31
          document.getElementById(formID + 45).value = second["M11461"]; // Rows ID 45
          document.getElementById(formID + 144).value = second["M11382"]; // R144	Vario cut off length (auto)
          jQuery(formID + 144).change();
        } catch (error) {
          console.log(error);
        }
        break;

      default:
        alert("Die Catecogr is not filled");
        break;
    }

    //Full diaphragm area
    let diaphragmArea = document.getElementById(formID + 210).value;
    console.log(diaphragmArea);
    if (diaphragmArea == "Diaphragm available") {
      try {
        let first = await keytech_information(
          materialNumberValue,
          strClassification,
          strName,
          "M13267",
          strValue
        );
        let result = first["M13267"];
        document
          .getElementsByName("input_185")[0]
          .setAttribute("value", parseFloat(result.replace(/,/g, "."))); //Full diaphragm area
        document.getElementById(formID + 185).value = parseFloat(
          result.replace(/,/g, ".")
        ); //Full diaphragm area
        jQuery("#input_12_185").change();
      } catch (error) {
        console.log(error);
      }
    }

    // Effective area input_id 40
    let effectiveProcess = document.getElementById(formID + 213).value;
    console.log("effective process ID40 =" + effectiveProcess);
    if (effectiveProcess == "Sealing surface") {
      try {
        let first = await keytech_information(
          materialNumberValue,
          strClassification,
          strName,
          "M13274",
          strValue
        );
        let result = first["M13274"];
        console.log("Parse 1 " + parseFloat(result.replace(/,/g, ".")));
        document
          .getElementsByName("input_40")[0]
          .setAttribute("value", parseFloat(result.replace(/,/g, ".")));

        jQuery("#input_12_40")
          .val(parseFloat(result.replace(/,/g, ".")))
          .change();
        jQuery("#input_12_185").change();
      } catch (error) {
        console.log(error);
      }
    } else if (effectiveProcess == "Heating surface") {
      let first = await keytech_information(
        materialNumberValue,
        strBom,
        strDescription,
        "Heizplatte",
        strName
      );
      console.log(first);
      let second = await keytech_information(
        first["Heizplatte"],
        strBom,
        strDescription,
        "Heizplatte",
        strName
      );
      console.log(second);
      let third = await keytech_information(
        second["Heizplatte"],
        strClassification,
        strName,
        "M11800,M11820",
        strValue
      );
      console.log(third);
      try {
        let resM11800 = third["M11800"];
        let resM11820 = third["M11820"];
        let calc = Math.round(
          parseFloat(resM11800.replace(/,/g, ".")) *
            parseFloat(resM11820.replace(/,/g, "."))
        );
        console.log("math calc - " + calc);
        // document.getElementsByName("input_40")[0].setAttribute("value",calc.toString());
        document.getElementsByName("input_40")[0].value = calc;
      } catch (error) {
        console.log(error);
      }
    }

    // heating plate id 105
    let heatingAvailagble = document.getElementById(formID + 209).value;
    console.log(heatingAvailagble);
    if (heatingAvailagble == "Heating plate") {
      let first = await keytech_information(
        materialNumberValue,
        strClassification,
        strName,
        "M11689",
        strValue
      );
      console.log("M11698 = " + first["M11689"]);
      if (first["M11689"] == "RHK") {
        const valueSAP = "tubular_heating_element"; // "Rohrheizkörper";
        var ddlArray = new Array();
        var ddl = document.getElementById(formID + 105);
        for (i = 0; i < ddl.options.length; i++) {
          ddlArray[i] = ddl.options[i].value;

          if (ddl.options[i].value == valueSAP) {
            document.getElementById(formID + 105).selectedIndex = i;
            console.log("heating plate ID 105 = Rohrheizkörper");
            jQuery(formID + 105).change();
            break;
          }
        }

        console.log(ddlArray);
      } else if (first["M11689"] == "Flachheizkörper") {
        // console.log("M11689 = Flachheizkörper");
        // document.getElementById(formID+105).value = "Flachheizelement";
        // jQuery( formID+105 ).change();
        const valueSAP2 = "flat_heating_element"; // "Flachheizelement";
        var ddlArray = new Array();
        var ddl = document.getElementById(formID + 105);
        for (i = 0; i < ddl.options.length; i++) {
          ddlArray[i] = ddl.options[i].value;

          if (ddl.options[i].value == valueSAP2) {
            document.getElementById(formID + 105).selectedIndex = i;
            console.log("heating plate ID 105 = Flachheizelement");
            jQuery(formID + 105).change();
            break;
          }
        }
      }
    } else if (heatingAvailagble == "Cartidge heating") {
      try {
        let first = await keytech_information(
          materialNumberValue,
          strBom,
          strName,
          "107534930",
          strCount
        );
        console.log(first);
        console.log(first["107534930"]);
        if (first["107534930"] > 0) {
          document.getElementById(formID + 105).value = "Heizpartrone 300 Watt";
        }
      } catch (error) {
        console.log(error);
      }

      try {
        let first = await keytech_information(
          materialNumberValue,
          strBom,
          strName,
          "107534840",
          strCount
        );
        console.log(first);
        if (first["107534840"] > 0) {
          document.getElementById(formID + 105).value = "Heizpartrone 200 Watt";
        }
      } catch (error) {
        console.log(error);
      }
    }
    //Number of springs (auto) 180
    //read ID 211: Spring force available (pop) = "Spring force available"
    let SpringForce = document.getElementById(formID + "211").value;
    console.log("ID 211 - Spring force value is: " + SpringForce);

    if (SpringForce == "Spring force available") {
      try {
        let first = await keytech_information(
          materialNumberValue,
          strBom,
          strName,
          "19781202000",
          strCount
        );
        console.log(first["19781202000"]);
        console.log(first);
        console.log("Druckfeder count 19781202000 = " + first["19781202000"]);
        var numberOfSprings1 = first["19781202000"];
      } catch (error) {
        console.log(error);
        var numberOfSprings1 = 0;
      }

      try {
        let first = await keytech_information(
          materialNumberValue,
          strBom,
          strName,
          "102029523",
          strCount
        );
        console.log("Druckfeder count 102029523 = " + first["102029523"]);
        console.log(first);
        var numberOfSprings2 = first["102029523"];
      } catch (error) {
        console.log(error);
        var numberOfSprings2 = 0;
      }
      if (numberOfSprings1 > 0 && numberOfSprings2 > 0) {
        console.log("We have  19781202000 and 102029523 ");
      } else if (numberOfSprings1 > 0) {
        console.log("Druckfeder count(19781202000) = " + numberOfSprings1);
        document.getElementById(formID + 180).value = numberOfSprings1; // Number of springs (auto) 180
        jQuery(formID + 180).change();
      } else {
        console.log("Druckfeder count(102029523) = " + numberOfSprings2);
        document.getElementById(formID + 180).value = numberOfSprings2; // Number of springs (auto) 180
        jQuery(formID + 180).change();
      }
    }

    //241	Edge evacuation type (auto)
    try {
      // ID 256: Sealing stroke (pop) ≠ "na"
      let sealingStroke = document.getElementById(formID + "256").value;
      console.log(sealingStroke);
      if (sealingStroke != "na") {
        let first = await keytech_information(
          materialNumberValue,
          strClassification,
          strName,
          "M13270",
          strValue
        );
        document.getElementById(formID + 241).value = first["M13270"];
        jQuery(formID + 241).change();
        jQuery("#input_12_241").change();
        jQuery("input_12_241").change();
        jQuery("input_12_241").change();
      }
    } catch (error) {
      console.log(error);
    }
      
    //TODO : We dont have 'preloding in html'
    // preloading.style.display = "none";
    alert("Done");
  }

  /** PACK information autofill function */

  async function autofillPackInformation() {
    //splash screen start
    const preloading = document.getElementById("preloading");
    preloading.style.display = "block";

    // Format sketch info
    try {
      const numberFormatSketch = document.getElementById(formID + "237").value;
      console.log(numberFormatSketch);
      const names = "M11576,M11577,M11578,M11579,M11580,M11581";
      let first = await keytech_Format_sketch_information(
        numberFormatSketch,
        strName,
        names,
        strValue
      );
      
      document.getElementById(formID + "34").value = first["M11576"].replace(
        ",",
        "."
      );
      jQuery(formID + "34").change();
      document.getElementById(formID + "35").value = first["M11577"].replace(
        ",",
        "."
      );
      jQuery(formID + "35").change();
      document.getElementById(formID + "239").value = first["M11578"].replace(
        ",",
        "."
      );
      jQuery(formID + "239").change();
      document.getElementById(formID + "240").value = first["M11579"].replace(
        ",",
        "."
      );
      jQuery("input_12_240").change();
      jQuery("#input_12_240").change();
      // document.getElementById("input_12_240").change();
      document.getElementById(formID + "236").value = first["M11580"].replace(
        ",",
        "."
      );
      jQuery(formID + "236").change();
      document.getElementById(formID + "235").value = first["M11581"].replace(
        ",",
        "."
      );
      jQuery(formID + "235").change();
      jQuery("input_12_235").change();
      jQuery("#input_12_235").change();
    } catch (error) {
      console.log(error);
    }

    preloading.style.display = "none";
    alert("Done");
  }

  async function buttonConfirm() {
    let text =
      "This function will get the values from SAP and overwrite existing. Do you really want to do this ?";

    if (confirm(text) == true) {
      console.log("You pressed OK!");
      await autofill();
    } else {
      console.log("You canceled!");
    }
  }

  async function confirmPack() {
    let text =
      "This function will get the pack information values from SAP and overwrite existing. Do you really want to do this ?";

    if (confirm(text) == true) {
      console.log("You pressed OK!");
      await autofillPackInformation();
    } else {
      console.log("You canceled!");
    }
  }

  async function keytech_information(id, type, searchBy, Name, returnResult) {
    // check if input data is JSON

    var communication_file =
      "/jsonexcel/Autofill/keytech_communication_comb_v2.php";
    let result;

    try {
      result = await jQuery.ajax({
        url: communication_file,
        type: "POST",
        data: {
          id: id,
          type: type,
          search_by: searchBy,
          names: Name,
          return: returnResult,
        },
      });

      return JSON.parse(result);
    } catch (error) {
      console.error(error);
      return false;
    }
  }

  async function keytech_Format_sketch_information(
    id,
    searchBy,
    names,
    returnResult
  ) {
    // check if input data is JSON

    var communication_file = "/jsonexcel/Autofill/keytech_format_sketch.php";
    let result;

    try {
      result = await jQuery.ajax({
        url: communication_file,
        type: "POST",
        data: {
          id: id,
          search_by: searchBy,
          names: names,
          return: returnResult,
        },
      });

      return JSON.parse(result);
    } catch (error) {
      console.error(error);
    }
  }


  /** run function autofill when button is clicked */
   check = document
     .getElementById("field_12_206")
     .addEventListener("click", buttonConfirm);

  // /** Pack information autofill */

   checkPack = document
     .getElementById("field_12_274")
     .addEventListener("click", confirmPack);
});
