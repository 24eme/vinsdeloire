<%@ taglib prefix="fn" uri="http://java.sun.com/jsp/jstl/functions" %>
<%@ taglib prefix="form" uri="http://www.springframework.org/tags/form" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ page contentType="text/html; charset=UTF-8" %>
<jsp:directive.include file="includes/top.jsp" />
                                <p class="intro">
                                        Bienvenue dans l'espace d&eacute;di&eacute; aux professionnels du Vignoble du Val de Loire. <br />
                                        Acc&eacute;dez au nouvel outil de t&eacute;l&eacute;-d&eacute;claration de vos contrats ainsi qu'aux donn&eacute;es et informations de l'observatoire &eacute;conomique.
                                </p>

                                <img class="visuel" src="images/vi_authentification.png" alt="D&eacute;clarez vos contrats en ligne. Consultez l'observatoire &eacute;conomique." />

                                <img class="visuel_mobile" src="images/vi_authentification_mobile.png" alt="D&eacute;clarez vos contrats en ligne. Consultez l'observatoire &eacute;conomique." />

                                <div class="authentification">
                                        <div class="bloc bloc_connexion">
                                                <h2>Connexion</h2>

                                                <div class="bloc_contenu">
                                                        <p>Entrez votre identifiant et votre mot de passe</p>
<form:form method="post" id="fm1" cssClass="fm-v clearfix" commandName="${commandName}" htmlEscape="true">

<form:errors path="*" id="msg" cssClass="errors" element="div" htmlEscape="false" />

                    <div class="form_ligne">
                        <label for="username" class="fl-label"><spring:message code="screen.welcome.label.netid" /></label>
						<c:if test="${not empty sessionScope.openIdLocalId}">
						<strong>${sessionScope.openIdLocalId}</strong>
						<input type="hidden" id="username" name="username" value="${sessionScope.openIdLocalId}" />
						</c:if>

						<c:if test="${empty sessionScope.openIdLocalId}">
						<spring:message code="screen.welcome.label.netid.accesskey" var="userNameAccessKey" />
						<form:input cssClass="required champ" cssErrorClass="error" id="username" size="25" tabindex="1" accesskey="${userNameAccessKey}" path="username" autocomplete="false" htmlEscape="true" />
						</c:if>
                    </div>
                    <div class="form_ligne">
                        <label for="password" class="fl-label"><spring:message code="screen.welcome.label.password" /></label>
						<%--
						NOTE: Certain browsers will offer the option of caching passwords for a user.  There is a non-standard attribute,
						"autocomplete" that when set to "off" will tell certain browsers not to prompt to cache credentials.  For more
						information, see the following web page:
						http://www.geocities.com/technofundo/tech/web/ie_autocomplete.html
						--%>
						<spring:message code="screen.welcome.label.password.accesskey" var="passwordAccessKey" />
						<form:password cssClass="required champ" cssErrorClass="error" id="password" size="25" tabindex="2" path="password"  accesskey="${passwordAccessKey}" htmlEscape="true" autocomplete="off" />
                    </div>
                    <div class="form_btn">
                                                                <div class="form_ligne txt_droite">
                                                                        <a href="https://teledeclaration.vinsvaldeloire.pro/mot_de_passe_oublie" class="mdp_oublie">Mot de passe oubli&eacute; ?</a>
                                                                </div>


                                                                <div class="form_ligne txt_centre">
                                                                        <button class="btn_majeur btn_vert" accesskey="l" tabindex="4" type="submit" type="submit">Valider</button>
                                                                </div>
						<input type="hidden" name="lt" value="${flowExecutionKey}" />
						<input type="hidden" name="_eventId" value="submit" />
                    </div>
            </form:form>
</div>
<div class="bloc bloc_inscription">
  <h2>Premi&egrave;re connexion</h2>
  <div class="bloc_contenu">
    <p>S'il s'agit de votre premi&egrave;re connexion, munissez vous de votre num&eacute;ro interloire et du code &agrave; 4 chiffres de cr&eacute;ation re&ccedil;us par courrier.</p>

<div class="form_ligne txt_centre">
  <a href="https://teledeclaration.vinsvaldeloire.pro/teledeclarant/code_creation" class="btn_majeur btn_orange">Cr&eacute;er votre compte</a>
</div>
</div>
</div>

</div>
<jsp:directive.include file="includes/bottom.jsp" />
