<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/17/2018
 * Time: 9:05 PM
 */

class FORMS
{
    function __construct()
    {

    }

    public function generatePromiseButton($dogInfo, $playInfo)
    {
        $button = "<div class='itemEditButton'>" . PHP_EOL;

        // check if its promise is enabled
        $promiseClass = 'promise-normal';
        if ($dogInfo["adopted"] == 'yes') $promiseClass = 'promise-selected';
        $button .= "<button class='itemButton $promiseClass' " . PHP_EOL;

        // check if the user is the same as the user who promised
        $params = $dogInfo['id'] . ", " . $playInfo['id'];

        $buttonLabel = "PROMISE";
        $onClick = "onclick='onSelectPromise(" . $params . ")'>";
        if ($dogInfo['adopted_by'] != $playInfo['team_id']
        && $dogInfo["adopted"] == 'yes')
        {
            $buttonLabel = "PROMISED";
            $onClick = "onclick='onPromiseDisabledAlert(" . $params . ")'>";
        }
        else
        {
            if ($dogInfo['adopted_by'] == $playInfo['team_id']
                && $dogInfo["adopted"] == 'yes')
            {
                $buttonLabel = "PROMISED";
                $onClick = "onclick='onDeSelectPromise(" . $params . ")'>";
            }
            else
            {
                $buttonLabel = "PROMISE";
                $onClick = "onclick='onSelectPromise(" . $params . ")'>";
            }
        }

        $button .= $onClick . PHP_EOL;
        $button .= "<span class=\"fas fa-star\"></span>&nbsp;&nbsp;$buttonLabel". PHP_EOL;
        $button .= "</button>" . PHP_EOL;

        $button .= "</div>" . PHP_EOL;

        return $button;
    }

    public function checkFieldIsEmpty($field)
    {
        if (empty($field))
        {
            return "Nothing Added.";
        }

        return $field;
    }

    public function generateAgeShortLabel($ageInMonths)
    {
        $ageLabel = $ageInMonths;

        if ($ageInMonths < 12)
        {
            $ageLabel = ($ageInMonths > 1) ? $ageInMonths . " mo" : $ageInMonths . " mo";
            if ($ageInMonths == 0) $ageLabel = "Newborn";

        }
        else
        {
            $monthValue = $ageInMonths / 12;
            $monthLabel = $monthValue . '';
            if (strrpos($monthLabel, ".") == false)
            {
                $monthValue = floor($ageInMonths / 12);
                $ageLabel = ($monthValue > 1) ? $monthLabel . " yrs" : $monthLabel . " yr";
            }
        }

        return $ageLabel;
    }

    public function generateAgeLabel($ageInMonths)
    {
        $ageLabel = $ageInMonths;

        if ($ageInMonths < 12)
        {
            $ageLabel = ($ageInMonths > 1) ? $ageInMonths . " months old" : $ageInMonths . " month old";
            if ($ageInMonths == 0) $ageLabel = "Newborn";

        }
        else
        {
            $monthValue = $ageInMonths / 12;
            $monthLabel = $monthValue . '';
            if (strrpos($monthLabel, ".") == false)
            {
                $monthValue = floor($ageInMonths / 12);
                $ageLabel = ($monthValue > 1) ? $monthLabel . " years old" : $monthLabel . " year old";
            }
        }

        return $ageLabel;
    }

    public function generateAgeSelectOptions($selectedAge)
    {
        if (is_null($selectedAge))
        {
            $selectedAge = 0;
        }

        $divElement = '';
        $monthAmount = 18;

        for ($mIndex = 0; $mIndex < ($monthAmount * 12); $mIndex++) {

            if ($mIndex < 12) {
                $ageLabel = ($mIndex > 1) ? $mIndex . " months old" : $mIndex . " month old";
                if ($mIndex == 0) $ageLabel = "Newborn";

                $selected = ($mIndex == $selectedAge) ? "selected" : "";
                $div = "<option $selected value='$mIndex'>$ageLabel</option>";
                //echo $div;

                $divElement .= $div . PHP_EOL;
            } else {
                $monthValue = $mIndex / 12;
                $monthLabel = $monthValue . '';
                if (strrpos($monthLabel, ".") == false) {
                    $monthValue = floor($mIndex / 12);
                    $ageLabel = ($monthValue > 1) ? $monthLabel . " years old" : $monthLabel . " year old";

                    $selected = ($mIndex == $selectedAge) ? "selected" : "";
                    $div = "<option $selected value='$mIndex'>$ageLabel</option>";
                    //echo $div;

                    $divElement .= $div . PHP_EOL;
                }
            }
        }

        return $divElement;
    }

    public function generateGenderOptions($selectedGender)
    {
        if (is_null($selectedGender))
        {
            $selectedGender = "male";
        }

        $divElement = '';
        $genders = array("male", "female");
        for ($i = 0; $i < count($genders); $i++) {
            $genderLabel = $genders[$i];
            $selected = ($genders[$i] == $selectedGender) ? "selected" : "";
            $divElement .= "<option $selected value='$genders[$i]'>".ucwords($genderLabel)."</option>";
            $divElement .= PHP_EOL;
        }

        return $divElement;
    }

    public function generateFixedOptions($selectedFix)
    {
        if (is_null($selectedFix))
        {
            $selectedFix = "intact";
        }

        $divElement = '';
        $fixed = array("spayed", "neutured", "intact");
        for ($i = 0; $i < count($fixed); $i++) {
            $fixedLabel = $fixed[$i];
            $selected = ($fixed[$i] == $selectedFix) ? "selected" : "";
            $divElement .= "<option $selected value='$fixed[$i]'>".ucwords($fixedLabel)."</option>";
            $divElement .= PHP_EOL;
        }

        return $divElement;
    }

    public function generateRoleOptions($selectedRole)
    {
        if (is_null($selectedRole)) $selectedRole = "admin";

        $divElement = '';
        $roles = array("admin", "team");
        for ($i = 0; $i < count($roles); $i++) {
            $label = $roles[$i];
            $selected = ($roles[$i] == $selectedRole) ? "selected" : "";
            $divElement .= "<option $selected value='$roles[$i]'>".ucwords($label)."</option>";
            $divElement .= PHP_EOL;
        }

        return $divElement;
    }

}