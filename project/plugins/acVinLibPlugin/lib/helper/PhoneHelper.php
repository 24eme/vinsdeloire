<?php

function formatPhone($phone) {
    
    return preg_replace('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '\1.\2.\3.\4.\5', $phone);
}