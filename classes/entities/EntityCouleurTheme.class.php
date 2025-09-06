<?php

class EntityCouleurTheme
{
    private int $id;
    private string $Pseudo;
    private string $theme;
    private string $color1;
    private string $color2;
    private string $color3;
    private string $color4;
    private string $bgcolor;
    private string $darkcolor;

    /**
     * Get the value of theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Get the value of color1
     */
    public function getColor1()
    {
        return $this->color1;
    }

    /**
     * Get the value of color2
     */
    public function getColor2()
    {
        return $this->color2;
    }

    /**
     * Get the value of color3
     */
    public function getColor3()
    {
        return $this->color3;
    }

    /**
     * Get the value of color4
     */
    public function getColor4()
    {
        return $this->color4;
    }

    /**
     * Get the value of bgcolor
     */
    public function getBgcolor()
    {
        return $this->bgcolor;
    }

    /**
     * Get the value of darkcolor
     */
    public function getDarkcolor()
    {
        return $this->darkcolor;
    }
}
