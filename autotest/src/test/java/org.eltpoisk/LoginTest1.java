package org.eltpoisk;

import io.github.bonigarcia.wdm.WebDriverManager;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import static org.hamcrest.MatcherAssert.assertThat;

import static org.hamcrest.Matchers.containsString;

public class LoginTest1 {
    WebDriver driver;

    @BeforeAll
    static void setupAll() {
        WebDriverManager.chromedriver().setup();
    }

    @BeforeEach
    void setup() {
        driver = new ChromeDriver();
    }

    @AfterEach
    void tearDown() {
        driver.quit();
    }

    @Test
    void test() {

        //Arrange
        // подготовительный действия
        //Act - действия
        driver.get("https://bonigarcia.dev/selenium-webdriver-java/");
        final String title = driver.getTitle();

        // Assert - проверочка
        assertThat(title,  containsString("Selenium WebDriver"));
    }
}
