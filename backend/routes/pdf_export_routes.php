<?php

use controllers\PdfExportController;

Flight::route('GET /prets/@id/export-pdf', [PdfExportController::class, 'exportLoanPayments']);
Flight::route('GET /prets/@id/payment-data', [PdfExportController::class, 'getLoanPaymentData']);
