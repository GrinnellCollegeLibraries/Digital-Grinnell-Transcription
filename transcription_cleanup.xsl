<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="1.0">
    <xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" media-type="text/xml"/>
    <xsl:template match="//relatedItem[@displayLabel='No Transcription Available']"/>
    <xsl:template match="//relatedItem[@type='constituent' and not(@displayLabel)]"/>
</xsl:stylesheet>