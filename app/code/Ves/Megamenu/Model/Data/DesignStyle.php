<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Ves\Megamenu\Model\Data;

use Ves\Megamenu\Api\Data\DesignStyleInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @codeCoverageIgnore
 */
class DesignStyle extends AbstractExtensibleObject implements DesignStyleInterface
{
    /**
     * @inheritDoc
     */
    public function getMarginTop()
    {
        return $this->_get(self::MARGIN_TOP);
    }

    /**
     * @inheritDoc
     */
    public function setMarginTop($margin_top)
    {
        return $this->setData(self::MARGIN_TOP, $margin_top);
    }

    /**
     * @inheritDoc
     */
    public function getMarginRight()
    {
        return $this->_get(self::MARGIN_RIGHT);
    }

    /**
     * @inheritDoc
     */
    public function setMarginRight($margin_right)
    {
        return $this->setData(self::MARGIN_RIGHT, $margin_right);
    }

    /**
     * @inheritDoc
     */
    public function getMarginBottom()
    {
        return $this->_get(self::MARGIN_BOTTOM);
    }

    /**
     * @inheritDoc
     */
    public function setMarginBottom($margin_bottom)
    {
        return $this->setData(self::MARGIN_BOTTOM, $margin_bottom);
    }

    /**
     * @inheritDoc
     */
    public function getMarginLeft()
    {
        return $this->_get(self::MARGIN_LEFT);
    }

    /**
     * @inheritDoc
     */
    public function setMarginLeft($margin_left)
    {
        return $this->setData(self::MARGIN_LEFT, $margin_left);
    }

    /**
     * @inheritDoc
     */
    public function getMarginUnits()
    {
        return $this->_get(self::MARGIN_UNITS);
    }

    /**
     * @inheritDoc
     */
    public function setMarginUnits($margin_units)
    {
        return $this->setData(self::MARGIN_UNITS, $margin_units);
    }

    /**
     * @inheritDoc
     */
    public function getBorderTopWidth()
    {
        return $this->_get(self::BORDER_TOP_WIDTH);
    }

    /**
     * @inheritDoc
     */
    public function setBorderTopWidth($border_top_width)
    {
        return $this->setData(self::BORDER_TOP_WIDTH, $border_top_width);
    }

    /**
     * @inheritDoc
     */
    public function getBorderBottomWidth()
    {
        return $this->_get(self::BORDER_BOTTOM_WIDTH);
    }

    /**
     * @inheritDoc
     */
    public function setBorderBottomWidth($border_bottom_width)
    {
        return $this->setData(self::BORDER_BOTTOM_WIDTH, $border_bottom_width);
    }

    /**
     * @inheritDoc
     */
    public function getBorderLeftWidth()
    {
        return $this->_get(self::BORDER_LEFT_WIDTH);
    }

    /**
     * @inheritDoc
     */
    public function setBorderLeftWidth($border_left_width)
    {
        return $this->setData(self::BORDER_LEFT_WIDTH, $border_left_width);
    }

    /**
     * @inheritDoc
     */
    public function getBorderUnits()
    {
        return $this->_get(self::BORDER_UNITS);
    }

    /**
     * @inheritDoc
     */
    public function setBorderUnits($border_units)
    {
        return $this->setData(self::BORDER_UNITS, $border_units);
    }

     /**
     * Get padding_top
     * @return string|null
     */
    public function getPaddingTop()
    {
        return $this->_get(self::PADDING_TOP);
    }

    /**
     * Set padding_top
     * @param string $padding_top
     * @return $this
     */
    public function setPaddingTop($padding_top)
    {
        return $this->setData(self::PADDING_TOP, $padding_top);
    }

    /**
     * Get padding_right
     * @return string|null
     */
    public function getPaddingRight()
    {
        return $this->_get(self::PADDING_RIGHT);
    }

    /**
     * Set padding_right
     * @param string $padding_right
     * @return $this
     */
    public function setPaddingRight($padding_right)
    {
        return $this->setData(self::PADDING_RIGHT, $padding_right);
    }

    /**
     * Get padding_bottom
     * @return string|null
     */
    public function getPaddingBottom()
    {
        return $this->_get(self::PADDING_BOTTOM);
    }

    /**
     * Set padding_bottom
     * @param string $padding_bottom
     * @return $this
     */
    public function setPaddingBottom($padding_bottom)
    {
        return $this->setData(self::PADDING_BOTTOM, $padding_bottom);
    }

    /**
     * Get padding_left
     * @return string|null
     */
    public function getPaddingLeft()
    {
        return $this->_get(self::PADDING_LEFT);
    }

    /**
     * Set padding_left
     * @param string $padding_left
     * @return $this
     */
    public function setPaddingLeft($padding_left)
    {
        return $this->setData(self::PADDING_LEFT, $padding_left);
    }

    /**
     * Get padding_units
     * @return string|null
     */
    public function getPaddingUnits()
    {
        return $this->_get(self::PADDING_UNITS);
    }

    /**
     * Set padding_units
     * @param string $padding_units
     * @return $this
     */
    public function setPaddingUnits($padding_units)
    {
        return $this->setData(self::PADDING_UNITS, $padding_units);
    }

    /**
     * Get width
     * @return string|null
     */
    public function getWidth()
    {
        return $this->_get(self::WIDTH);
    }

    /**
     * Set width
     * @param string $width
     * @return $this
     */
    public function setWidth($width)
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * Get border_color
     * @return string|null
     */
    public function getBorderColor()
    {
        return $this->_get(self::BORDER_COLOR);
    }

    /**
     * Set border_color
     * @param string $border_color
     * @return $this
     */
    public function setBorderColor($border_color)
    {
        return $this->setData(self::BORDER_COLOR, $border_color);
    }

    /**
     * Get border_style
     * @return string|null
     */
    public function getBorderStyle()
    {
        return $this->_get(self::BORDER_STYLE);
    }

    /**
     * Set border_style
     * @param string $border_style
     * @return $this
     */
    public function setBorderStyle($border_style)
    {
        return $this->setData(self::BORDER_STYLE, $border_style);
    }

    /**
     * Get background
     * @return string|null
     */
    public function getBackground()
    {
        return $this->_get(self::BACKGROUND);
    }

    /**
     * Set background
     * @param string $background
     * @return $this
     */
    public function setBackground($background)
    {
        return $this->setData(self::BACKGROUND, $background);
    }

    /**
     * Get boxshadow_units
     * @return string|null
     */
    public function getBoxshadowUnits()
    {
        return $this->_get(self::BOXSHADOW_UNITS);
    }

    /**
     * Set boxshadow_units
     * @param string $boxshadow_units
     * @return $this
     */
    public function setBoxshadowUnits($boxshadow_units)
    {
        return $this->setData(self::BOXSHADOW_UNITS, $boxshadow_units);
    }

    /**
     * Get boxshadow_inset
     * @return string|null
     */
    public function getBoxshadowInset()
    {
        return $this->_get(self::BOXSHADOW_INSET);
    }

    /**
     * Set boxshadow_inset
     * @param string $boxshadow_inset
     * @return $this
     */
    public function setBoxshadowInset($boxshadow_inset)
    {
        return $this->setData(self::BOXSHADOW_INSET, $boxshadow_inset);
    }

    /**
     * Get boxshadow_x
     * @return string|null
     */
    public function getBoxshadowX()
    {
        return $this->_get(self::BOXSHADOW_X);
    }

    /**
     * Set boxshadow_x
     * @param string $boxshadow_x
     * @return $this
     */
    public function setBoxshadowX($boxshadow_x)
    {
        return $this->setData(self::BOXSHADOW_X, $boxshadow_x);
    }

    /**
     * Get boxshadow_y
     * @return string|null
     */
    public function getBoxshadowY()
    {
        return $this->_get(self::BOXSHADOW_Y);
    }

    /**
     * Set boxshadow_y
     * @param string $boxshadow_y
     * @return $this
     */
    public function setBoxshadowY($boxshadow_y)
    {
        return $this->setData(self::BOXSHADOW_Y, $boxshadow_y);
    }

    /**
     * Get boxshadow_blur
     * @return string|null
     */
    public function getBoxshadowBlur()
    {
        return $this->_get(self::BOXSHADOW_BLUR);
    }

    /**
     * Set boxshadow_blur
     * @param string $boxshadow_blur
     * @return $this
     */
    public function setBoxshadowBlur($boxshadow_blur)
    {
        return $this->setData(self::BOXSHADOW_BLUR, $boxshadow_blur);
    }

    /**
     * Get boxshadow_spread
     * @return string|null
     */
    public function getBoxshadowSpread()
    {
        return $this->_get(self::BOXSHADOW_SPREAD);
    }

    /**
     * Set boxshadow_spread
     * @param string $boxshadow_spread
     * @return $this
     */
    public function setBoxshadowSpread($boxshadow_spread)
    {
        return $this->setData(self::BOXSHADOW_SPREAD, $boxshadow_spread);
    }

    /**
     * Get boxshadow_color
     * @return string|null
     */
    public function getBoxshadowColor()
    {
        return $this->_get(self::BOXSHADOW_COLOR);
    }

    /**
     * Set boxshadow_color
     * @param string $boxshadow_color
     * @return $this
     */
    public function setBoxshadowColor($boxshadow_color)
    {
        return $this->setData(self::BOXSHADOW_COLOR, $boxshadow_color);
    }

    /**
     * Get font_size
     * @return string|null
     */
    public function getFontSize()
    {
        return $this->_get(self::FONT_SIZE);
    }

    /**
     * Set font_size
     * @param string $font_size
     * @return $this
     */
    public function setFontSize($font_size)
    {
        return $this->setData(self::FONT_SIZE, $font_size);
    }

    /**
     * Get font_group
     * @return string|null
     */
    public function getFontGroup()
    {
        return $this->_get(self::FONT_GROUP);
    }

    /**
     * Set font_group
     * @param string $font_group
     * @return $this
     */
    public function setFontGroup($font_group)
    {
        return $this->setData(self::FONT_GROUP, $font_group);
    }

    /**
     * Get font_custom
     * @return string|null
     */
    public function getFontCustom()
    {
        return $this->_get(self::FONT_CUSTOM);
    }

    /**
     * Set font_custom
     * @param string $font_custom
     * @return $this
     */
    public function setFontCustom($font_custom)
    {
        return $this->setData(self::FONT_CUSTOM, $font_custom);
    }

    /**
     * Get font_google
     * @return string|null
     */
    public function getFontGoogle()
    {
        return $this->_get(self::FONT_GOOGLE);
    }

    /**
     * Set font_google
     * @param string $font_google
     * @return $this
     */
    public function setFontGoogle($font_google)
    {
        return $this->setData(self::FONT_GOOGLE, $font_google);
    }

    /**
     * Get font_char_subset
     * @return string|null
     */
    public function getFontCharSubset()
    {
        return $this->_get(self::FONT_CHAR_SUBSET);
    }

    /**
     * Set font_char_subset
     * @param string $font_char_subset
     * @return $this
     */
    public function setFontCharSubset($font_char_subset)
    {
        return $this->setData(self::FONT_CHAR_SUBSET, $font_char_subset);
    }

    /**
     * Get font_weight
     * @return string|null
     */
    public function getFontWeight()
    {
        return $this->_get(self::FONT_WEIGHT);
    }

    /**
     * Set font_weight
     * @param string $font_weight
     * @return $this
     */
    public function setFontWeight($font_weight)
    {
        return $this->setData(self::FONT_WEIGHT, $font_weight);
    }

    /**
     * Get border_top_left_radius
     * @return string|null
     */
    public function getBorderTopLeftRadius()
    {
        return $this->_get(self::BORDER_TOP_LEFT_RADIUS);
    }

    /**
     * Set border_top_left_radius
     * @param string $border_top_left_radius
     * @return $this
     */
    public function setBorderTopLeftRadius($border_top_left_radius)
    {
        return $this->setData(self::BORDER_TOP_LEFT_RADIUS, $border_top_left_radius);
    }

    /**
     * Get border_top_right_radius
     * @return string|null
     */
    public function getBorderTopRightRadius()
    {
        return $this->_get(self::BORDER_TOP_RIGHT_RADIUS);
    }

    /**
     * Set border_top_right_radius
     * @param string $border_top_right_radius
     * @return $this
     */
    public function setBorderTopRightRadius($border_top_right_radius)
    {
        return $this->setData(self::BORDER_TOP_RIGHT_RADIUS, $border_top_right_radius);
    }

    /**
     * Get border_bottom_right_radius
     * @return string|null
     */
    public function getBorderBottomRightRadius()
    {
        return $this->_get(self::BORDER_BOTTOM_RIGHT_RADIUS);
    }

    /**
     * Set border_bottom_right_radius
     * @param string $border_bottom_right_radius
     * @return $this
     */
    public function setBorderBottomRightRadius($border_bottom_right_radius)
    {
        return $this->setData(self::BORDER_BOTTOM_RIGHT_RADIUS, $border_bottom_right_radius);
    }

    /**
     * Get border_bottom_left_radius
     * @return string|null
     */
    public function getBorderBottomLeftRadius()
    {
        return $this->_get(self::BORDER_BOTTOM_LEFT_RADIUS);
    }

    /**
     * Set border_bottom_left_radius
     * @param string $border_bottom_left_radius
     * @return $this
     */
    public function setBorderBottomLeftRadius($border_bottom_left_radius)
    {
        return $this->setData(self::BORDER_BOTTOM_LEFT_RADIUS, $border_bottom_left_radius);
    }

    /**
     * Get radius_units
     * @return string|null
     */
    public function getRadiusUnits()
    {
        return $this->_get(self::RADIUS_UNITS);
    }

    /**
     * Set radius_units
     * @param string $radius_units
     * @return $this
     */
    public function setRadiusUnits($radius_units)
    {
        return $this->setData(self::RADIUS_UNITS, $radius_units);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Ves\Megamenu\Api\Data\DesignStyleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
