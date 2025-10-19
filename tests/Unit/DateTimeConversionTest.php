<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DateTimeConversionTest extends TestCase
{
    /**
     * Test datetime conversion from database format to input format
     *
     * @return void
     */
    public function test_database_to_input_format_conversion()
    {
        // Test database format: "YYYY-MM-DD HH:MM:SS"
        $dbFormat = "2025-10-18 03:02:00";
        
        // Expected input format: "YYYY-MM-DDTHH:MM"
        $expected = "2025-10-18T03:02";
        
        // Simulate the conversion in the controller
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $dbFormat);
        $result = $date->format('Y-m-d\TH:i');
        
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test datetime conversion from input format to database format
     *
     * @return void
     */
    public function test_input_to_database_format_conversion()
    {
        // Test input format: "YYYY-MM-DDTHH:MM"
        $inputFormat = "2025-10-18T03:02";
        
        // Expected database format: "YYYY-MM-DD HH:MM:SS"
        $expected = "2025-10-18 03:02:00";
        
        // Simulate the conversion in the controller
        $result = str_replace('T', ' ', $inputFormat) . ':00';
        
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test datetime parsing in controller
     *
     * @return void
     */
    public function test_datetime_parsing_in_controller()
    {
        // Simulate the date parsing logic in the controller's update method
        $inputDate = "2025-10-18T03:02";
        
        // Convert to database format
        $dbFormat = str_replace('T', ' ', $inputDate) . ':00';
        
        // Parse with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m-d H:i:s', $dbFormat);
        
        $this->assertInstanceOf(Carbon::class, $parsedDate);
        $this->assertEquals("2025-10-18 03:02:00", $parsedDate->format('Y-m-d H:i:s'));
    }
}