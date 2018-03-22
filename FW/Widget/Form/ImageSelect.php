<?php

namespace FW\Widget\Form;

/**
 * 
 * 
 */
class ImageSelect extends FormElement 
{
    
    /**
     * Ссылка на изображение
     * @var string 
     */
    private $_img_link;
    

    /**
     * 
     * @var string
     */
    private $_delete_handler = '';
            
    
    
    /**
     * Конструктор
     * 
     * @param string $img_link Ссылка на изображение
     */
    public function __construct($img_link) 
    {
        $this->_img_link = $img_link;
    }
    
    
    /**
     * 
     * 
     * @param string $uri
     * @return \FW\Widget\Form\ImageSelect
     */
    public function setDeleteHandler($uri)
    {
        $this->_delete_handler = $uri;
        return $this;
    }
    
    
    /**
     * Вывод виджета
     * 
     * @return string
     */
    public function __toString() 
    {
        $output = '';
        if (!empty($this->_img_link)) {
            $output  = "<div class='form-img'>";
            $output .= "<img src='" . $this->_img_link . "' />";
            $output .= "<span class='remove' id='image-select-delete-img'>&times;</span>";
            $output .= "</div>";
        }
        $output .= "<input name='" . $this->_name . "' type='file' ";
        $output .= "class='" . $this->_classes . "' value='' />";
        $output .= $this->_getWidgetTemplate('ImageSelect', [
            'id'    =>  'image-select-delete-img',
            'uri'   =>  $this->_delete_handler
        ]);
        
        return $output;
    }

}